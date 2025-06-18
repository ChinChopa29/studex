<?php 

namespace App\Services;

use App\Models\Message;
use App\Models\MessageFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageService
{
   protected function getUserData()
   {
      $user = Auth::user();
      return [
         'user' => $user,
         'userType' => get_class($user),
      ];
   }

   public function getMessageForUser()
   {
      $userData = $this->getUserData();
      $userId = $userData['user']->id;
      $userType = $userData['userType'];

      return Message::where('receiver_id', $userId)
         ->where('receiver_type', $userType)
         ->whereNull('deleted_by_receiver_at') 
         ->whereNull('deleted_at') 
         ->orderBy('created_at', 'desc') 
         ->paginate(10);
   }

   public function showMessage(Message $message) 
   {
      $userData = $this->getUserData();
      $user = $userData['user'];
      $userType = $userData['userType'];
   
      $isReceiver = $message->receiver_id === $user->id && $message->receiver_type === $userType;
      $isSender = $message->sender_id === $user->id && $message->sender_type === $userType;
   
      $isFavorite = DB::table('favorite_messages')
         ->where('user_id', $user->id)
         ->where('user_type', $userType)
         ->where('message_id', $message->id)
         ->exists();
   
      if (!$isReceiver && !$isSender && !$isFavorite) {
         abort(403, 'У вас нет доступа к этому сообщению.');
      }
   
      if ($isReceiver && $message->status == 0) {
         $message->update(['status' => 1]);
      }
   
      return $message->load('files');
   }

   public function showSendedMessages() 
   {
      $user = Auth::user();
      $userType = get_class($user);
   
      return $messages = Message::where('sender_id', $user->id)
                        ->where('sender_type', $userType)
                        ->whereNull('deleted_by_sender_at') 
                        ->orderBy('created_at', 'desc') 
                        ->paginate(10);
   }

   public function showSendedMessage(Message $message) 
   {
      $userData = $this->getUserData();
      $user = $userData['user'];
      $userType = $userData['userType'];
   
      if ($message->sender_id !== $user->id || $message->sender_type !== $userType) {
         abort(403, 'У вас нет доступа к этому сообщению.');
      }
   
      return $message->load('files');
   }

   public function showDeletedMessages() 
   {
      $user = Auth::user();
    
      $messages = Message::where(function ($query) use ($user) {
               $query->where('receiver_id', $user->id)
                     ->whereNotNull('deleted_by_receiver_at')
                     ->where('deleted_by_receiver', false); 
         })
         ->orWhere(function ($query) use ($user) {
               $query->where('sender_id', $user->id)
                     ->whereNotNull('deleted_by_sender_at')
                     ->where('deleted_by_sender', false); 
         })
         ->orderBy('created_at', 'desc') 
         ->paginate(10);

      return $messages;
   }

   public function showDeletedMessage(Message $message)
   {
      $user = Auth::user();
      $userType = get_class($user);
  
      if (is_null($message->deleted_by_receiver_at) && is_null($message->deleted_by_sender_at)) {
          abort(403, 'Сообщение не было удалено.');
      }
  
      if ($message->receiver_id !== $user->id && $message->sender_id !== $user->id) {
          abort(403, 'У вас нет доступа к этому сообщению.');
      }

      return $message;
   }

   public function showFavoriteMessages() 
   {
      $user = Auth::user();
      $userType = get_class($user);
   
      $favoriteMessageIds = DB::table('favorite_messages')
         ->where('user_id', $user->id)
         ->where('user_type', $userType)
         ->pluck('message_id'); 
   
      $messages = Message::whereIn('id', $favoriteMessageIds)
                        ->orderBy('created_at', 'desc') 
                        ->paginate(10);
      
      return $messages;
   }

   public function acceptCourseInvite($message) 
   {
      $student = Auth::user();
      $courseId = $message->additional;
  
      if (!$courseId) {
         return redirect()->back()->with('error', 'Ошибка: курс не найден.');
      }

      $student->courses()
          ->wherePivot('course_id', $courseId)
          ->wherePivot('status', 'declined')
          ->detach();
  
      $student->courses()->syncWithoutDetaching([
          $courseId => ['status' => 'accepted']
      ]);
  
      $message->forceDelete();
  
      return redirect()->back()->with('success', 'Курс успешно принят.');
   }

  public function declineCourseInvite($message) 
   {
      $student = Auth::user();
      $courseId = $message->additional;
      
      if (!$courseId) {
         return redirect()->back()->with('error', 'Ошибка: курс не найден.');
      }
   
      $student->courses()->syncWithoutDetaching([
         $courseId => ['status' => 'declined']
      ]);
   
      return $message->delete();
   }

   public function searchRecievers($request, $type)
   {
      $query = $request->input('query');
    
      if (!$query) {
         return response()->json([]);
      }
   
      $words = explode(' ', trim($query)); 
   
      if ($type === 'student') {
         $users = \App\Models\Student::query()->with('groups'); 
      } elseif ($type === 'teacher') {
         $users = \App\Models\Teacher::query();
      } else {
         return response()->json([]);
      }
   
      foreach ($words as $word) {
         $users->where(function ($q) use ($word) {
               $q->where('name', 'LIKE', "%{$word}%")
               ->orWhere('surname', 'LIKE', "%{$word}%")
               ->orWhere('lastname', 'LIKE', "%{$word}%");
         });
      }
   
      $results = $users->limit(5)->get(['id', 'name', 'surname', 'lastname']);
   
      if ($type === 'student') {
         $results->transform(function ($student) {
               $groups = $student->groups->pluck('name')->implode(', '); 
               $student->full_name = "{$student->surname} {$student->name} {$student->lastname} ({$groups})";
               return $student;
         });
      } else {
         $results->transform(function ($teacher) {
               $teacher->full_name = "{$teacher->surname} {$teacher->name} {$teacher->lastname}";
               return $teacher;
         });
      }

      return $results;
   }

   public function createMessage($request)
   {
      $request->validated();

      $sender = Auth::guard('admin')->user()
         ?? Auth::guard('teacher')->user()
         ?? Auth::guard('student')->user();

      if (!$sender) {
         return ['status' => 'error', 'message' => 'Ошибка авторизации.'];
      }

      $receiverModel = match ($request->receiver_type) {
         'student' => \App\Models\Student::find($request->receiver_id),
         'teacher' => \App\Models\Teacher::find($request->receiver_id),
         'admin' => \App\Models\User::find($request->receiver_id),
      };

      if (!$receiverModel && $request->receiver_type !== 'admin') {
         return ['status' => 'error', 'message' => 'Получатель не найден.'];
      }

      $receiverType = match ($request->receiver_type) {
         'student' => \App\Models\Student::class,
         'teacher' => \App\Models\Teacher::class,
         'admin' => 'App\Models\Admin', 
      };

      $message = Message::create([
         'receiver_id' => $receiverModel?->id,
         'receiver_type' => $receiverType,
         'sender_id' => $sender->id,
         'sender_type' => get_class($sender),
         'message' => $request->message,
         'type' => 'text',
         'status' => false,
      ]);

      if ($request->hasFile('files')) {
         foreach ($request->file('files') as $file) {
               $path = $file->store('message_files', 'public');

               MessageFile::create([
                  'message_id' => $message->id,
                  'file_path' => $path,
               ]);
         }
      }

      return ['status' => 'success', 'message' => 'Сообщение отправлено.'];
   }

   public function searchMessages($query, $mailType)
   {
      $user = Auth::user();
      $messages = Message::with('sender');
      $view = match($mailType) {
         'sent' => $this->filterSent($messages, $user),
         'favorite' => $this->filterFavorite($messages, $user),
         'deleted' => $this->filterDeleted($messages, $user),
         default => $this->filterInbox($messages, $user),
      };

      $this->applySearchFilters($messages, $query);

      if (empty($query)) {
         $messages->orderBy('created_at', 'desc');
      }

      return [
         'messages' => $messages->paginate(10)->appends(request()->query()),
         'view' => $view,
      ];
   }

   private function filterSent($query, $user): string
   {
      $query->where('sender_id', $user->id)
            ->where('sender_type', get_class($user))
            ->whereNull('deleted_by_sender_at');
      return 'sended-mail';
   }

   private function filterFavorite($query, $user): string
   {
      $query->whereHas('favorites', function ($q) use ($user) {
         $q->where('user_id', $user->id)
            ->where('user_type', get_class($user));
      });
      return 'favorite-messages';
   }

   private function filterDeleted($query, $user): string
   {
      $query->where(function ($builder) use ($user) {
         $builder->where(function ($q) use ($user) {
               $q->where('receiver_id', $user->id)
               ->whereNotNull('deleted_by_receiver_at')
               ->where('deleted_by_receiver', false);
         })->orWhere(function ($q) use ($user) {
               $q->where('sender_id', $user->id)
               ->whereNotNull('deleted_by_sender_at')
               ->where('deleted_by_sender', false);
         });
      });
      return 'recent-deleted-mail';
   }

   private function filterInbox($query, $user): string
   {
      $query->where('receiver_id', $user->id)
            ->where('receiver_type', get_class($user))
            ->whereNull('deleted_by_receiver_at');
      return 'mail';
   }

   private function applySearchFilters($query, $search)
   {
      $query->where(function ($q) use ($search) {
         $q->where('message', 'LIKE', "%$search%")
            ->orWhereDate('created_at', $search)
            ->orWhereHas('sender', function ($q) use ($search) {
               $q->where('surname', 'LIKE', "%$search%")
                  ->orWhere('name', 'LIKE', "%$search%");
            });
      });
   }
}