<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMessageController extends Controller
{
    /** List all other admins with last message + unread count */
    public function index()
    {
        $me = Auth::guard('admin')->user();
        $admins = Admin::where('id', '!=', $me->id)->get()->map(function ($admin) use ($me) {
            $lastMsg = AdminMessage::where(function ($q) use ($me, $admin) {
                    $q->where('from_admin_id', $me->id)->where('to_admin_id', $admin->id);
                })->orWhere(function ($q) use ($me, $admin) {
                    $q->where('from_admin_id', $admin->id)->where('to_admin_id', $me->id);
                })->latest()->first();

            $unread = AdminMessage::where('from_admin_id', $admin->id)
                ->where('to_admin_id', $me->id)
                ->whereNull('read_at')
                ->count();

            $admin->last_message = $lastMsg;
            $admin->unread = $unread;
            return $admin;
        })->sortByDesc(function ($a) { return optional($a->last_message)->created_at; })->values();

        return view('admin.messages.index', compact('admins', 'me'));
    }

    /** Show thread between me and another admin */
    public function show($adminId)
    {
        $me = Auth::guard('admin')->user();
        $other = Admin::findOrFail($adminId);

        // Mark all incoming messages as read
        AdminMessage::where('from_admin_id', $adminId)
            ->where('to_admin_id', $me->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = AdminMessage::where(function ($q) use ($me, $adminId) {
                $q->where('from_admin_id', $me->id)->where('to_admin_id', $adminId);
            })->orWhere(function ($q) use ($me, $adminId) {
                $q->where('from_admin_id', $adminId)->where('to_admin_id', $me->id);
            })->orderBy('created_at')->get();

        return view('admin.messages.show', compact('messages', 'other', 'me'));
    }

    /** Send a message */
    public function store(Request $request, $adminId)
    {
        $request->validate(['body' => 'required|string|max:2000']);
        $me = Auth::guard('admin')->user();

        AdminMessage::create([
            'from_admin_id' => $me->id,
            'to_admin_id'   => $adminId,
            'body'          => $request->body,
        ]);

        return redirect()->route('admin.messages.show', $adminId);
    }
}
