<?php

namespace App\Http\Controllers;

use App\ReferralTender;
use App\User;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ReferralTender::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $referralTender = ReferralTender::create($request->all());

        return response()->json($referralTender, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ReferralTender  $referralTender
     * @return \Illuminate\Http\Response
     */
    public function show(ReferralTender $referral)
    {
        return $referral;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ReferralTender  $referralTender
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReferralTender $referral)
    {
        $referral->update($request->all());

        return \response()->json($referral, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ReferralTender  $referralTender
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReferralTender $referral)
    {
        $referral->levels()->delete();
        $referral->delete();

        return response()->json(null, 204);
    }


    /**
     * Get current referral tender
     * 
     * @return \Illuminate\Http\Response
     */
    public function current()
    {
        $now = now()->format('Y-m-d');
        $tender = ReferralTender::where('date_from', '<=', $now)->where('date_to', '>=', $now)->first();
        return response()->json($tender->load('levels'), 200);
    }

    public function invited(Request $request)
    {
        $userId = $request->get('user_id');
        $tenderId = $request->get('referral_tender_id');
        $user = User::find($userId);
        $invited = $user->referrals()->where('referral_tender_id', $tenderId);
        return response()->json($invited, 200);
    }
}
