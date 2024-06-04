<?php

namespace App\Livewire;

use App\Models\Chat;
use App\Models\Contact;
use App\Models\Message;
use App\Models\User;
use App\Notifications\newMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Ramsey\Collection\Collection;

class ChatComponent extends Component
{

    public $search;
    public $userContacts = [];
    public $contactChat, $chat, $bodyMessage, $chat_id;
    public $users;

    public function mount(){
        $this->users = collect();
    }

    public function getContactsProperty()
    {
        return $this->userContacts = Contact::Where('user_id', Auth()->id())->when($this->search, function ($query) {
            $query->where(function ($query) {
                $query->where('name', 'like', "%" . $this->search . "%")
                    ->orWhereHas('user', function ($query) {
                        $query->where('email', 'like', "%" . $this->search . "%");
                    });
            });
        })->get() ?? [];
    }

    public function open_chat_contact(Contact $userContact)
    {
        $chat =  auth()->user()->chats()
            ->whereHas('user', function ($query) use ($userContact) {
                $query->where('users.id', $userContact->contact_id);
            })
            ->has('user', 2)
            ->first();
        if ($chat) {
            $this->chat = $chat;
            $this->chat_id = $chat->id;
            $this->reset('contactChat', 'bodyMessage', 'search');
        } else {
            $this->contactChat = $userContact;
            $this->reset('chat', 'bodyMessage', 'search');
        }
    }

    public function open_chat(Chat $chat)
    {
        $this->chat_id = $chat->id;
        $this->chat = $chat;
    }


    public function readMessage() {
        $isRead = $this->chat->messages()->where('user_id','!=',auth()->id())->where('is_read',false)->update(
            ['is_read' => true]
        );
        if($isRead)
            Notification::send($this->getUserNotification(), new \App\Notifications\readMessage());
    }

    public function sendMessage()
    {
        $validation = $this->validate([
            'bodyMessage' => 'required'
        ], [
            'bodyMessage.required' => 'El campo bodyMessage es obligatorio.'
        ]);

        if (!$this->chat) {
            $this->chat = Chat::create();
            $this->chat_id = $this->chat->id;
            $this->chat->user()->attach([auth()->user()->id, $this->contactChat->contact_id]);
        }

        Notification::send($this->getUserNotification(), new \App\Notifications\newMessage());
        $this->isNotTyping();
        $this->chat->messages()->create([
            'body' => $this->bodyMessage,
            'user_id' => auth()->id()
        ]);

        $this->reset('bodyMessage', 'contactChat');

    }

    public function getMessages()
    {
        if ($this->chat) {
            return Message::where('chat_id', $this->chat->id)->get();
        }
        return [];
    }

    public function getChats()
    {
        return auth()->user()->chats()->get()->sortByDesc('getTimeLastMessage');
    }

    public function getUserNotification()
    {
        return $this->chat ? $this->chat->user->where('id', '!=', auth()->id()) : collect();
    }

    public function getListeners()
    {
        $userId = auth()->id();
        return [
            "echo-notification:App.Models.User.{$userId},notification" => "render",
            "echo-presence:chat.1,here" => 'chatHere',
            "echo-presence:chat.1,joining" => 'chatJoining',
            "echo-presence:chat.1,leaving" => 'chatLeaving',
        ];
    }

    public function updating($property, $value)
    {
        if ($property === 'bodyMessage') {
            if(isset($this->chat->id)) {
                Notification::send($this->getUserNotification(), new \App\Notifications\UserTyping($this->chat->id,true));
            }
        }
    }

    public function isNotTyping(){
        if(isset($this->chat->id)){
            Notification::send($this->getUserNotification(), new \App\Notifications\UserTyping($this->chat->id,false));
        }
    }

    public function chatHere($users){
        $this->users = Collect($users)->pluck('id');
    }

    public function chatJoining($user) {
        $this->users->push($user['id']);
    }

    public function getActiveUsersProperty($userId){
        $valueToCheck = is_int($userId) ? $userId : $userId[0]->user_id;
        return $this->users->contains($valueToCheck);
    }

    public function getUserChat($chat){
       return DB::table('chat_user')->select('user_id')->where('user_id', '!=', auth()->id())->where('chat_id',$chat)->get();
    }

    public function chatLeaving($user) {
        $this->users =  $this->users->filter(function ($item) use($user) {
            if ($item != $user['id']) {
                return $item;
            }
        });
    }


    public function render()
    {
        if ($this->chat) {
            $this->readMessage();
            $this->dispatch('doScroll');
        }
        return view('livewire.chat-component')->layout('layouts.chat');
    }
}
