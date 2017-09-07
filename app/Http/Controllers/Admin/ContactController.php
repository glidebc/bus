<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Contact;
use App\Http\Requests\CreateContactRequest;
use App\Http\Requests\UpdateContactRequest;
use Illuminate\Http\Request;



class ContactController extends Controller {

	/**
	 * Display a listing of contact
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
        $contact = Contact::all();

		return view('admin.contact.index', compact('contact'));
	}

	/**
	 * Show the form for creating a new contact
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
	    
	    
	    return view('admin.contact.create');
	}

	/**
	 * Store a newly created contact in storage.
	 *
     * @param CreateContactRequest|Request $request
	 */
	public function store(CreateContactRequest $request)
	{
	    
		Contact::create($request->all());

		return redirect()->route(config('quickadmin.route').'.contact.index');
	}

	/**
	 * Show the form for editing the specified contact.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$contact = Contact::find($id);
	    
	    
		return view('admin.contact.edit', compact('contact'));
	}

	/**
	 * Update the specified contact in storage.
     * @param UpdateContactRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdateContactRequest $request)
	{
		$contact = Contact::findOrFail($id);

        

		$contact->update($request->all());

		return redirect()->route(config('quickadmin.route').'.contact.index');
	}

	/**
	 * Remove the specified contact from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Contact::destroy($id);

		return redirect()->route(config('quickadmin.route').'.contact.index');
	}

    /**
     * Mass delete function from index page
     * @param Request $request
     *
     * @return mixed
     */
    public function massDelete(Request $request)
    {
        if ($request->get('toDelete') != 'mass') {
            $toDelete = json_decode($request->get('toDelete'));
            Contact::destroy($toDelete);
        } else {
            Contact::whereNotNull('id')->delete();
        }

        return redirect()->route(config('quickadmin.route').'.contact.index');
    }

}
