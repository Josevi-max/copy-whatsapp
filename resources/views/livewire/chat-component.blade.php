<div class="bg-gray-50 rounded-lg shadow border border-gray-200 overflow-hidden">
    <div x-data="{
        chat_id: @entangle('chat_id'),
        typingChatId: false,
        init() {
            Echo.private(`App.Models.User.` + {{ auth()->id() }})
                .notification((notification) => {
                    if (notification.type == 'App\\Notifications\\UserTyping') {
                        console.log('escribiendo');
                        this.typingChatId = notification.isTyping;
                    }
                });
        }
    }" class="grid grid-cols-3 divide-x divide-gray-200">


        <div class="col-span-1">

            <div class="bg-gray-100 h-16 flex items-center px-4">

                <img class="w-10 h-10 object-cover object-center" src="{{ auth()->user()->profile_photo_url }}"
                    alt="{{ auth()->user()->name }}">
            </div>

            <div class="h-14 flex items center bg-white px-4 pt-3 pb-3 ">
                <x-input wire:model.live.debounce.250ms="search" type="text" class="w-full"
                    placeholder="Busca chat o inicia uno nuevo">
                </x-input>
            </div>

            <div class="h-[calc(100vh-10.5rem)] border-t border-gray-200 overflow-auto">

                @if ($this->getChats()->count() == 0 || $this->search)
                    <div class="px-4 py-3">
                        <h2 class="text-teal-600 text lg">Contáctos</h2>

                        <ul class="space-y-4 pt-4">
                            @forelse ($this->getContactsProperty() as $contact)
                                <li class="cursor-pointer" wire:click="open_chat_contact({{ $contact }})">
                                    <div class="flex">
                                        <figure class="flex-shrink-0">
                                            <img class="h-12 w-12 object-cover object-center rounded-full"
                                                src="{{ $contact->user->profile_photo_url }}"
                                                alt="{{ $contact->name }}">
                                        </figure>

                                        <div class="flex-1 ms-5 border-b border-gray-200">
                                            <p class="text-gray-800">
                                                {{ $contact->name }}
                                            </p>
                                            <p class="text-gray-600 text-xs">
                                                {{ $contact->user->email }}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @empty
                            @endforelse
                        </ul>
                    </div>
                @else
                    @foreach ($this->getChats() as $chatItem)
                        <div wire:key="chats-{{ $chatItem->id }}" wire:click="open_chat({{ $chatItem }})"
                            class="{{ $chat && $chat->id == $chatItem->id ? 'bg-gray-100' : 'bg-white' }}  flex items-center  hover:bg-gray-100 cursor-pointer px-3"
                            wire:key>
                            <figure>
                                <img src="{{ $chatItem->image }}"
                                    class="h-12 w-12 object-cover object-center rounded-full"
                                    alt="{{ $chatItem->mame }}">
                            </figure>
                            <div class="ml-4 w-[calc(100%-4rem)] py-4 text-cs border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <p class="text-dark">
                                        {{ $chatItem->name }}
                                    </p>
                                    <p class="text-xs">
                                        {{ $chatItem->getTimeLastMessage }}
                                    </p>
                                </div>
                                <div class="flex justify-between items-center">
                                    <p class="text-sm  text-gray-700 mt-1  overflow-hidden text-ellipsis text-nowrap">
                                        {{ $chatItem->messages->last()->body }}
                                    </p>
                                    @if ($chatItem->unred_messages > 0)
                                        <span
                                            class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-500 text-white mt-3">
                                            {{ $chatItem->unred_messages }}
                                        </span>
                                    @endif

                                </div>

                            </div>
                        </div>
                    @endforeach
                @endif

            </div>

        </div>

        <div class="col-span-2  w-full  ">


            @if ($contactChat || $chat)
                <div class="flex items-center bg-gray-100 ps-3">
                    <figure>
                        @if ($chat)
                            <img class="w-10 h-10 rounded-full object-cover object-center" src="{{ $chat->image }}"
                                alt="{{ $chat->name }}">
                        @else
                            <img class="w-10 h-10 rounded-full object-cover object-center"
                                src="{{ $contactChat->user->profile_photo_url }}" alt="{{ $contactChat->name }}">
                        @endif
                    </figure>

                    <div class="ml-4 mt-2">
                        <p class="text-gray-800">
                            @if ($chat)
                                {{ $chat->name }}
                            @else
                                {{ $contactChat->name }}
                            @endif
                        </p>
                        @if ($this->getActiveUsersProperty(isset($contactChat->id) ? $contactChat->id : $this->getUserChat($chat->id)))
                            <p class="text-green-500" x-show="!typingChatId">
                                Online
                            </p>
                            <p class="text-gray-600" x-show="typingChatId">
                                Escribiendo...
                            </p>
                        @else
                            <p class="text-red-800" x-show="!typingChatId">
                                Offline
                            </p>
                        @endif
                        
                    </div>
                </div>

                <div class="h-[calc(100vh-10rem)] overflow-auto px-4 text-dark chat">
                    @foreach ($this->getMessages() as $message)
                        <div
                            class="{{ $message->user_id == auth()->id() ? 'justify-end' : 'justify-start' }} flex  mt-2 mb-2">
                            <div
                                class="rounded px-3 py-2 {{ $message->user_id == auth()->id() ? 'bg-green-100' : 'bg-gray-100' }} ">
                                <p class="text-sm">{{ $message->body }}</p>
                                <p class="text-right text-xs mt-1 text-gray-600 flex items-center justify-end">
                                    {{ $message->created_at->format('d-m-Y h:i A') }}
                                    @if ($message->user_id == auth()->id())
                                        <svg viewBox="0 0 16 11" height="11" width="16"
                                            preserveAspectRatio="xMidYMid meet"
                                            class="ms-2 {{ $message->is_read ? 'text-blue-700' : '' }}" fill="none">
                                            <title>msg-dblcheck</title>
                                            <path
                                                d="M11.0714 0.652832C10.991 0.585124 10.8894 0.55127 10.7667 0.55127C10.6186 0.55127 10.4916 0.610514 10.3858 0.729004L4.19688 8.36523L1.79112 6.09277C1.7488 6.04622 1.69802 6.01025 1.63877 5.98486C1.57953 5.95947 1.51817 5.94678 1.45469 5.94678C1.32351 5.94678 1.20925 5.99544 1.11192 6.09277L0.800883 6.40381C0.707784 6.49268 0.661235 6.60482 0.661235 6.74023C0.661235 6.87565 0.707784 6.98991 0.800883 7.08301L3.79698 10.0791C3.94509 10.2145 4.11224 10.2822 4.29844 10.2822C4.40424 10.2822 4.5058 10.259 4.60313 10.2124C4.70046 10.1659 4.78086 10.1003 4.84434 10.0156L11.4903 1.59863C11.5623 1.5013 11.5982 1.40186 11.5982 1.30029C11.5982 1.14372 11.5348 1.01888 11.4078 0.925781L11.0714 0.652832ZM8.6212 8.32715C8.43077 8.20866 8.2488 8.09017 8.0753 7.97168C7.99489 7.89128 7.8891 7.85107 7.75791 7.85107C7.6098 7.85107 7.4892 7.90397 7.3961 8.00977L7.10411 8.33984C7.01947 8.43717 6.97715 8.54508 6.97715 8.66357C6.97715 8.79476 7.0237 8.90902 7.1168 9.00635L8.1959 10.0791C8.33132 10.2145 8.49636 10.2822 8.69102 10.2822C8.79681 10.2822 8.89838 10.259 8.99571 10.2124C9.09304 10.1659 9.17556 10.1003 9.24327 10.0156L15.8639 1.62402C15.9358 1.53939 15.9718 1.43994 15.9718 1.32568C15.9718 1.1818 15.9125 1.05697 15.794 0.951172L15.4386 0.678223C15.3582 0.610514 15.2587 0.57666 15.1402 0.57666C14.9964 0.57666 14.8715 0.635905 14.7657 0.754395L8.6212 8.32715Z"
                                                fill="currentColor"></path>
                                        </svg>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                    <span id="endChat"></span>
                </div>

                <form class="bg-gray-100 h-16 flex items-center px-4" wire:submit.prevent="sendMessage()">
                    <x-input type="text" wire:model.live="bodyMessage" class="w-full"
                        placeholder="Escribe un mensaje aquí" x-on:blur="$wire.isNotTyping()"></x-input>
                    <button class="flex-shrink-0 ml-4">
                        <svg viewBox="0 0 24 24" height="24" width="24" preserveAspectRatio="xMidYMid meet"
                            version="1.1" x="0px" y="0px" enable-background="new 0 0 24 24">
                            <title>send</title>
                            <path fill="currentColor"
                                d="M1.101,21.757L23.8,12.028L1.101,2.3l0.011,7.912l13.623,1.816L1.112,13.845 L1.101,21.757z">
                            </path>
                        </svg>
                    </button>
                </form>
            @else
                <div class="h-full flex justify-center items-center" style="opacity: 1;">
                    <div>
                        <img src="https://static.whatsapp.net/rsrc.php/v3/yX/r/dJq9qKG5lDb.png" width="320"
                            alt="">
                        <h1 class="text-center text-gray-500 text-2xl mt-4">Whatsapp para escritorio</h1>
                    </div>

                </div>
            @endif




        </div>

    </div>

    @push('js')
        <script type="module">
            document.addEventListener('livewire:init', () => {
                Livewire.on('doScroll', function(message) {
                    setTimeout(() => {
                        document.getElementById('endChat').scrollIntoView(true);
                    }, 100);
                });
            });
        </script>
    @endpush
</div>
