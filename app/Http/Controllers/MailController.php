<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MailController extends Controller
{
    public function index() {
        $user = Auth::user();
        $userType = get_class($user);
    
        $messages = Message::where('receiver_id', $user->id)
                           ->where('receiver_type', $userType)
                           ->whereNull('deleted_by_receiver_at') 
                           ->whereNull('deleted_at') 
                           ->orderBy('created_at', 'desc') 
                           ->paginate(10);
    
        return view('mail', compact('messages', 'user'));
    }

    public function show(Message $message) {
        $user = Auth::user();
        $userType = get_class($user);
    
        if ($message->receiver_id !== $user->id || $message->receiver_type !== $userType) {
            abort(403, 'У вас нет доступа к этому сообщению.');
        }
    
        if ($message->status == 0) {
            $message->update(['status' => 1]);
        }
    
        $message->load('files');
    
        return view('show.mail', compact('message'));
    }
    

    public function showSended(Message $message) {
        $user = Auth::user();
        $userType = get_class($user);
    
        if ($message->sender_id !== $user->id || $message->sender_type !== $userType) {
            abort(403, 'У вас нет доступа к этому сообщению.');
        }
    
        $message->load('files');
    
        return view('show.sended-mail', compact('message'));
    }
    
    
    public function acceptInvite(Message $message) {
        $student = Auth::user();
        $courseId = $message->additional;
        
        if (!$courseId) {
            return redirect()->back()->with('error', 'Ошибка: курс не найден.');
        }
    
        $student->courses()->wherePivot('course_id', $courseId)
            ->wherePivot('status', 'declined')
            ->detach();
    
        $student->courses()->attach($courseId, ['status' => 'accepted']);
    
        $message->forceDelete();
    
        return redirect()->route('studentCoursesIndex')->with('success', 'Вы успешно записались на курс.');
    }
    

    public function declineInvite(Message $message) {
        $student = Auth::user();
        $courseId = $message->additional;
        
        if (!$courseId) {
            return redirect()->back()->with('error', 'Ошибка: курс не найден.');
        }
    
        $student->courses()->attach($courseId, ['status' => 'declined']);
    
        $message->delete();
    
        return redirect()->route('studentCoursesIndex')->with('success', 'Вы отказались от записи на курс.');
    }

    public function searchUsers(Request $request, $type) {
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
    
        return response()->json($results);
    }
    
    public function create() {
        return view('add.send-mail');
    }

    public function store(Request $request) {
        $request->validate([
            'receiver_id' => 'required',
            'receiver_type' => 'required|in:student,teacher,admin',
            'message' => 'required|string',
            'files.*' => 'nullable|file|max:2048', 
        ]);
    
        $sender = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('student')->user();
    
        if (!$sender) {
            return back()->with('error', 'Ошибка авторизации.');
        }
    
        $receiverModel = match ($request->receiver_type) {
            'student' => \App\Models\Student::find($request->receiver_id),
            'teacher' => \App\Models\Teacher::find($request->receiver_id),
            'admin' => \App\Models\User::where('id', $request->receiver_id)->first(),
        };
        
    
        if (!$receiverModel && $request->receiver_type !== 'admin') {
            return back()->with('error', 'Получатель не найден.');
        }
    
        $receiverType = match ($request->receiver_type) {
            'student' => \App\Models\Student::class,
            'teacher' => \App\Models\Teacher::class,
            'admin' => 'App\Models\Admin', 
        };
    
        $message = Message::create([
            'receiver_id' => $receiverModel ? $receiverModel->id : null,
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
    
        return back()->with('success', 'Сообщение отправлено.');
    }

    public function sended() {
        $user = Auth::user();
        $userType = get_class($user);
    
        $messages = Message::where('sender_id', $user->id)
                           ->where('sender_type', $userType)
                           ->whereNull('deleted_by_sender_at') 
                           ->orderBy('created_at', 'desc') 
                           ->paginate(10);
    
        return view('sended-mail', compact('messages'));
    }
    
    

    public function recentDeleted() {
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
    
        return view('recent-deleted-mail', compact('messages'));
    }

    public function showDeleted(Message $message) {
        $user = Auth::user();
        $userType = get_class($user);
    
        if (is_null($message->deleted_by_receiver_at) && is_null($message->deleted_by_sender_at)) {
            abort(403, 'Сообщение не было удалено.');
        }
    
        if ($message->receiver_id !== $user->id && $message->sender_id !== $user->id) {
            abort(403, 'У вас нет доступа к этому сообщению.');
        }
    
        return view('show.deleted-mail', compact('message'));
    }

    public function bulkAction(Request $request) {
        $action = $request->input('action');
        $messageIds = $request->input('messages', []);
        $user = Auth::user();
        $userId = $user->id;
    
        if (empty($messageIds)) {
            return redirect()->back()->with('error', 'Выберите хотя бы одно сообщение.');
        }
    
        switch ($action) {
            case 'read':
                Message::whereIn('id', $messageIds)
                    ->where('receiver_id', $userId)
                    ->update(['status' => 1]);
                return redirect()->back()->with('success', 'Сообщения отмечены как прочитанные.');
    
            case 'delete':
                Message::whereIn('id', $messageIds)
                    ->where(function ($query) use ($userId) {
                        $query->where('receiver_id', $userId)
                                ->orWhere('sender_id', $userId);
                    })
                    ->update([
                        'status' => DB::raw("CASE WHEN receiver_id = $userId AND status = 0 THEN 1 ELSE status END"),
                        'deleted_by_receiver_at' => DB::raw("CASE WHEN receiver_id = $userId THEN NOW() ELSE deleted_by_receiver_at END"),
                        'deleted_by_sender_at' => DB::raw("CASE WHEN sender_id = $userId THEN NOW() ELSE deleted_by_sender_at END"),
                    ]);
            
                DB::table('favorite_messages')->whereIn('message_id', $messageIds)->delete();
            
                return redirect()->back()->with('success', 'Выбранные сообщения перемещены в корзину.');
                
    
            case 'forceDelete':
                Message::whereIn('id', $messageIds)
                    ->where(function ($query) use ($userId) {
                        $query->where('receiver_id', $userId)
                              ->orWhere('sender_id', $userId);
                    })
                    ->update([
                        'deleted_by_receiver' => DB::raw("CASE WHEN receiver_id = $userId THEN true ELSE deleted_by_receiver END"),
                        'deleted_by_sender' => DB::raw("CASE WHEN sender_id = $userId THEN true ELSE deleted_by_sender END"),
                    ]);
    
                return redirect()->back()->with('success', 'Выбранные сообщения полностью удалены.');

            case 'favorite':
                $user = Auth::user(); 
            
                $favorites = [];
                foreach ($messageIds as $messageId) {
                    $favorites[] = [
                        'user_id' => $user->id,
                        'user_type' => get_class($user), 
                        'message_id' => $messageId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            
                DB::table('favorite_messages')->insertOrIgnore($favorites);
            
                return redirect()->back()->with('success', 'Выбранные сообщения добавлены в избранное.');

            case 'unfavorite':
                DB::table('favorite_messages')
                    ->where('user_id', $userId)
                    ->where('user_type', get_class($user)) 
                    ->whereIn('message_id', $messageIds)
                    ->delete();
            
                return redirect()->back()->with('success', 'Выбранные сообщения удалены из избранного.');
                
            
            default:
                return redirect()->back()->with('error', 'Неизвестное действие.');
        }
    }

    public function favorite() {
        $user = Auth::user();
        $userType = get_class($user);
    
        $favoriteMessageIds = DB::table('favorite_messages')
            ->where('user_id', $user->id)
            ->where('user_type', $userType)
            ->pluck('message_id'); 
    
        $messages = Message::whereIn('id', $favoriteMessageIds)
                           ->orderBy('created_at', 'desc') 
                           ->paginate(10);
    
        return view('favorite-messages', compact('messages', 'user'));
    }
    
    
    public function search(Request $request) {
        $query = $request->get('search');
        $mailType = $request->get('mail_type'); 
        $user = Auth::user();
        
        $messages = Message::with('sender');
    
        switch ($mailType) {
            case 'sent':
                $messages->where('sender_id', $user->id)->where('sender_type', get_class($user));
                $view = 'sended-mail';
                break;
    
            case 'favorite':
                $messages->whereHas('favorites', function ($q) use ($user) {
                    $q->where('user_id', $user->id)->where('user_type', get_class($user));
                });
                $view = 'favorite-messages';
                break;
    
            case 'deleted':
                $messages->where(function ($query) use ($user) {
                        $query->where('receiver_id', $user->id)
                                ->whereNotNull('deleted_by_receiver_at')
                                ->where('deleted_by_receiver', false);
                    })
                    ->orWhere(function ($query) use ($user) {
                        $query->where('sender_id', $user->id)
                                ->whereNotNull('deleted_by_sender_at')
                                ->where('deleted_by_sender', false);
                    });
            
                $messages->where(function ($q) use ($query) {
                    $q->where('message', 'LIKE', "%$query%")
                        ->orWhereDate('created_at', $query)
                        ->orWhereHas('sender', function ($q) use ($query) {
                            $q->where('surname', 'LIKE', "%$query%")
                            ->orWhere('name', 'LIKE', "%$query%");
                        });
                });
            
                $view = 'recent-deleted-mail';
                break;
                
                
    
            default: 
                $messages->where('receiver_id', $user->id)->where('receiver_type', get_class($user));
                $view = 'mail';
                break;
        }
    
        $messages->where(function ($q) use ($query) {
            $q->where('message', 'LIKE', "%$query%")
              ->orWhereDate('created_at', $query);
        })
        ->orWhereHas('sender', function ($q) use ($query) {
            $q->where('surname', 'LIKE', "%$query%")
              ->orWhere('name', 'LIKE', "%$query%");
        });
    
        $messages = $messages->paginate(10)->appends(request()->query());
    
       
        return view($view, compact('messages'));
    }
}
