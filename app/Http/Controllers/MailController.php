<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMailRequest;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MessageBulkActionService;

class MailController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function index() 
    {
        $messages = $this->messageService->getMessageForUser();
        $user = Auth::user();
        return view('mail', compact('messages', 'user'));
    }

    public function show(Message $message) 
    {
        $message = $this->messageService->showMessage($message);
        return view('show.mail', compact('message'));
    }
    
    public function sended() 
    {
        $messages = $this->messageService->showSendedMessages();
        return view('sended-mail', compact('messages'));
    }

    public function showSended(Message $message) 
    {
        $this->messageService->showSendedMessage($message);
        return view('show.sended-mail', compact('message'));
    }

    public function create() 
    {
        return view('add.send-mail');
    }

    public function store(StoreMailRequest $request)
    {
        $result = $this->messageService->createMessage($request);
        return back()->with($result['status'], $result['message']);
    }

    public function acceptInvite(Message $message) 
    {
        $this->messageService->acceptCourseInvite($message);
        return redirect()->route('CoursesIndex')
            ->with('success', 'Вы успешно записались на курс.');
    }

    public function declineInvite(Message $message) 
    {
        $this->messageService->declineCourseInvite($message);
        return redirect()->route('CoursesIndex')
            ->with('success', 'Вы отказались от записи на курс.');
    }

    public function searchUsers(Request $request, $type) 
    {
        $results = $this->messageService->searchRecievers($request, $type);
        return response()->json($results);
    }
    
    public function recentDeleted() 
    {
        $messages = $this->messageService->showDeletedMessages();
        return view('recent-deleted-mail', compact('messages'));
    }

    public function showDeleted(Message $message) 
    {
        $message = $this->messageService->showDeletedMessage($message);
        return view('show.deleted-mail', compact('message'));
    }

    public function favorite() 
    {
        $messages = $this->messageService->showFavoriteMessages();
        $user = Auth::user();
        return view('favorite-messages', compact('messages', 'user'));
    }
    
    public function search(Request $request) 
    {
        $query = $request->get('search');
        $mailType = $request->get('mail_type'); 
        
        ['messages' => $messages, 'view' => $view] = $this->messageService->searchMessages($query, $mailType);

        return view($view, compact('messages'));
    }

    public function bulkAction(Request $request, MessageBulkActionService $service)
    {
        $action = $request->input('action');
        $messageIds = $request->input('messages', []);
        $user = Auth::user();

        if (empty($messageIds)) {
            return back()->with('error', 'Выберите хотя бы одно сообщение.');
        }

        try {
            $service->handle($action, $messageIds, $user);
            return back()->with('success', 'Операция выполнена успешно.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Что-то пошло не так.');
        }
    }
}
