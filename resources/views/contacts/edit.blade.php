<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contacts') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('contacts.update', $contact) }}" method="POST" class="mx-auto bg-white rounded-lg shadow p-6 max-w-4xl">
                @method('PATCH')
                @csrf
                <x-validation-errors class="mb-4"></x-validation-errors>
                <x-label class="mb-2 ">
                    Nombre de contacto
                </x-label>
                <x-input type="text" name="name" value="{{old('name',$contact->name)}}" class="w-full">
                </x-input>
                <x-label class="mt-4 mb-2">
                    Email de contacto
                </x-label>
                <x-input type="text" name="email" value="{{old('email',$contact->user->email)}}" class="w-full ">
                </x-input>
                
                <div class="flex justify-end text-end">
                    <x-button type="submit" class="mt-2 ">Actualizar</x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>