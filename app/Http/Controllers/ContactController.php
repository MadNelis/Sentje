<?php

namespace App\Http\Controllers;

use App\FavoritesGroup;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function editGroup($id)
    {
        $group = FavoritesGroup::find($id);

        // Check for correct user (rights)
        if (Auth::id() != $group->user_id) {
            return redirect('/dashboard')->with('error', 'Unauthorized');
        }

        return view('contacts.edit')->with('group', $group);
    }

    public function storeGroup(Request $request)
    {
        $this->validate($request, [
//            'email' => 'email:rfc,dns|exists:users,email'
            'groupname' => 'required'
        ]);

        $favorites_group = new FavoritesGroup();
        $favorites_group->name = $request->input('groupname');
        $favorites_group->user_id = Auth::id();
        $favorites_group->save();

        return redirect('/dashboard');
    }

    public function destroyGroup($id)
    {
        $group = FavoritesGroup::find($id);

        // Check for correct user (rights)
        if (Auth::id() != $group->user_id) {
            return redirect('/dashboard' . $group->id . '/edit')->with('error', __('text.unauthorized'));
        }

        $group->delete();

        return redirect('/dashboard')->with('success', __('text.group_deleted'));
    }

    public function addContact(Request $request)
    {
        // Check if email valid and exists in database
        $this->validate($request, [
            'email' => 'email:rfc,dns|exists:users,email'
        ]);
        $email = $request->input('email');

        // Get the user by email
        $user_to_add = User::where('email', $email)->first();


        $user_id = Auth::id();
        $user = User::find($user_id);

        // Check if the user is not in contacts already
        if ($user->contacts->contains($user_to_add->id)) {
            return redirect('/dashboard')->with('error', __('text.person_already_in_contacts'));
        }

        // Cannot add yourself
        if ($user_to_add->id == $user_id) {
            return redirect('/dashboard')->with('error', __('text.cannot_add_yourself'));
        }

        $user->contacts()->attach($user_to_add->id);
        $user->save();

        return redirect('/dashboard')->with('success', __('text.added_to_contacts'));
    }

    public function removeContact(Request $request)
    {
        $user = Auth::user();
        $user_to_remove_id = $request->input('id');

        $user->contacts()->detach($user_to_remove_id);

        return redirect('/dashboard')->with('success', __('text.contact_deleted'));
    }

    public function addUserToGroup(Request $request)
    {
        // Check if email valid and exists in database
        $this->validate($request, [
            'email' => 'email:rfc,dns|exists:users,email'
        ]);
        $email = $request->input('email');

        // Get the current group to add it to
        $group_id = $request->input('id');
        $group = FavoritesGroup::find($group_id);

        // Get the user by email
        $user_to_add = User::where('email', $email)->first();

        // Check if the user is not in the group
        if ($group->members->contains($user_to_add->id)) {
            return redirect('/contacts/' . $group_id . '/edit_group')
                ->with('error', __('text.person_already_in_group'));
        }

        // Cannot add yourself
        if ($user_to_add->id == $group->user->id) {
            return redirect('/contacts/' . $group_id . '/edit_group')
                ->with('error', __('text.cannot_add_yourself'));
        }

        // Add the user to the group
        $group->members()->attach($user_to_add->id);
        $group->save();

        return redirect('/contacts/' . $group_id . '/edit_group');
    }

    public function removeUserFromGroup(Request $request)
    {
        $group_id = $request->input('group_id');
        $group = FavoritesGroup::find($group_id);
        $user_id = $request->input('user_id');

        $group->members()->detach($user_id);

        return redirect('/contacts/' . $group_id . '/edit_group')
            ->with('success', __('text.person_removed_from_group'));
    }
}
