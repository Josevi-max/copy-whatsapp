<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contacts') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="text-end mx-auto  w-full">
                <a href="{{ route('contacts.create') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                    Añadir contacto
                </a>
            </div>

            <div class="overflow-hidden  sm:rounded-lg pt-5">
                @if ($contacts->count())

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        {{ __('Name') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        {{ __('Email') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3">

                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contacts as $contact)
                                    <tr
                                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $contact->name }}
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ $contact->user->email }}
                                        </td>
                                        <td class="px-6 py-4 flex justify-between">
                                            <a href="{{ route('contacts.edit', $contact) }}"
                                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline">{{ __('Edit') }}</a>

                                            <form action="{{ route('contacts.destroy', $contact) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-input type="submit" value="{{ __('Delete') }}" class="font-medium ms-5 text-red-600 dark:text-red-500 hover:underline"></x-input>
                                            </form>
                                            
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex mx-auto max-w-4xl w-full  overflow-hidden  rounded-lg shadow-md dark:bg-gray-800">
                        <div class="flex items-center justify-center w-12 bg-blue-500">
                            <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z" />
                            </svg>
                        </div>

                        <div class="px-4 py-2 -mx-3">
                            <div class="mx-3">
                                <span class="font-semibold text-blue-500 dark:text-blue-400">Vaya!</span>
                                <p class="text-sm text-gray-600 dark:text-gray-200">Parece que no tienes contactos
                                    agregados
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
