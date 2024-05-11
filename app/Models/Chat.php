<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_url',
        'is_group'
    ];


    
    public function name():Attribute
    {
        return new Attribute(
            get:function($value) {
                if($this->is_group){
                    return $value;
                }

                $user = $this->user()->where('users.id', '!=', auth()->id())->first();

                $contact = auth()->user()->contact()->where('contact_id', $user->id)->first();

                return $contact ? $contact->name : $user->email;
            }
        );
    }

    public function image():Attribute {
        return new Attribute(
            get:function(){

                if($this->is_group){
                    return Storage::url($this->image_url);
                } 

                $user = $this->user()->where('users.id', '!=', auth()->id())->first();

                return $user->profile_photo_url;
            }
        );
    }
    
    public function getTimeLastMessage():Attribute {
        return new Attribute(
            get:function(){
                $lastMessage = $this->messages()->latest()->first();
                return $lastMessage ? $lastMessage->created_at : null;
            }
        );
    }

    public function unredMessages():Attribute {
        return new Attribute(
            get:function(){
                return $this->messages()->where('user_id','!=',auth()->id())->where('is_read',false)->count();
            }
        );
    }
    //relation one to many

    public function messages() {
        return $this->HasMany(Message::class);
    }
    
    public function user() {
        return $this->belongsToMany(User::class);
    }
}
