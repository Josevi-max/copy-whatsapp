<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use App\Rules\InvalidEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = auth()->user()->contact()->paginate(10);

        return view('contacts.index', compact('contacts'));
    }

    
     /**
     * Store a newly created resource in storage.
     */
    public function create()
    {
        return view('contacts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dataValidated = $request->validate([
            'name' => "required|max:255",
            'email' => [
                'required',
                'email',
                'exists:users',
                Rule::notIn([Auth()->user()->email]),
                new InvalidEmail
            ]
        ]);

        $user = User::Where('email', $request->email)->first();

        $contact = Contact::create(
            [
                'name' => $request->name,
                'user_id' => Auth()->id(),
                'contact_id' => $user->id
            ]
        );

        session()->flash('flash.banner', 'Contacto creado exitosamente');
        session()->flash('flash.bannerStyle', 'success');


        return view('contacts.create', compact('contact'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact("contact"));
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $validateData = $request->validate([
            "name" => "required|max:255",
            'email' => [
                'required',
                'email',
                'exists:users',
                new InvalidEmail($contact->user->email),
                function($attribute, $value, $fail) use ($request) {
                    if(strtolower(auth()->user()->email) === strtolower($request->email)) {
                        $fail('El correo electrónico no puede ser igual al tuyo.');
                    }
                }
            ]
        ]);
        $user = User::Where('email', $request->email)->first();

        $contact->update([
            "name"=>$request->name,
            "contact_id"=> $user->id
        ]);

        session()->flash('flash.banner', 'Contacto actualizado exitosamente');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->back()->with("contact",$contact);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {

       $contact->delete();

       session()->flash('flash.banner', 'Contacto eliminado exitosamente');
       session()->flash('flash.bannerStyle', 'success');
       return back();
    }
}
