<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Redirect;
use Schema;
use App\Agent;
use App\Http\Requests\CreateAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use Illuminate\Http\Request;



class AgentController extends Controller {

	/**
	 * Display a listing of agent
	 *
     * @param Request $request
     *
     * @return \Illuminate\View\View
	 */
	public function index(Request $request)
    {
    	$agent = Agent::withTrashed()->orderBy('created_at', 'desc')->get();
        // $agent = Agent::all();
		return view('admin.agent.index', compact('agent'));
	}

	/**
	 * Show the form for creating a new agent
	 *
     * @return \Illuminate\View\View
	 */
	public function create()
	{
	    return view('admin.agent.create');
	}

	/**
	 * Store a newly created agent in storage.
	 *
     * @param CreateAgentRequest|Request $request
	 */
	public function store(CreateAgentRequest $request)
	{
		Agent::create($request->all());
		return redirect()->route(config('quickadmin.route').'.agent.index');
	}

	/**
	 * Show the form for editing the specified agent.
	 *
	 * @param  int  $id
     * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$agent = Agent::withTrashed()->find($id);
		return view('admin.agent.edit', compact('agent'));
	}

	/**
	 * Update the specified agent in storage.
     * @param UpdateAgentRequest|Request $request
     *
	 * @param  int  $id
	 */
	public function update($id, UpdateAgentRequest $request)
	{
		$agent = Agent::withTrashed()->findOrFail($id);
		$agent->update($request->all());
		return redirect()->route(config('quickadmin.route').'.agent.index');
	}

	/**
	 * agent 的 deleted_at 改成 null.
	 *
	 * @param  int  $id
	 */
	public function resetDelete($id)
	{
		Agent::withTrashed()->find($id)->restore();
		return redirect()->route(config('quickadmin.route').'.agent.index');
	}

	/**
	 * Remove the specified agent from storage.
	 *
	 * @param  int  $id
	 */
	public function destroy($id)
	{
		Agent::destroy($id);

		return redirect()->route(config('quickadmin.route').'.agent.index');
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
            Agent::destroy($toDelete);
        } else {
            Agent::whereNotNull('id')->delete();
        }

        return redirect()->route(config('quickadmin.route').'.agent.index');
    }

}
