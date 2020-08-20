<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ReferralTender;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class ReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $referralTenders = ReferralTender::all();
        return view('admin.referral.index', compact('referralTenders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.referral.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'ru_description' => 'required|string',
            'uz_description' => 'required|string',
            'total_pot' => 'required|integer'
        ]);
        $data = $request->all();
        $data['ru_description'] = str_replace('&nbsp;', ' ', $data['ru_description']);
        $data['uz_description'] = str_replace('&nbsp;', ' ', $data['uz_description']);
        $referralTender = ReferralTender::create($data);
        foreach ($request->get('levels') as $level) {
            $referralTender->levels()->create($level);
        }
        $image = $request->file('image_ru');
        if ($image) {
            $referralTender->image_ru = $this->saveImage($image);
            $referralTender->save();
        }
        $image = $request->file('image_uz');
        if ($image) {
            $referralTender->image_uz = $this->saveImage($image);
            $referralTender->save();
        }
        return redirect()->route('admin.referral.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ReferralTender $referral
     * @return \Illuminate\Http\Response
     */
    public function edit(ReferralTender $referral)
    {
        // $users = User::withCount(['referrals' => function (Builder $query) use ($referral) {
        //     $query->where('referral_tender_id', $referral->id);
        // }])->get();
        // $topReferrals = [];
        // foreach ($users as $user) {
        //     if ($user->referrals_count > 0) {
        //         $topReferrals[$user->name] = $user->referrals_count;
        //     }

        // }

        $referrals = User::where('referral_tender_id', $referral->id)->get();
        $topReferrals = [];
        foreach ($referrals as $referralUser) {
            if ($referralUser->referralFrom) {
                if (isset($topReferrals[$referralUser->referralFrom->name])) {
                    $topReferrals[$referralUser->referralFrom->name]++;
                } else {
                    $topReferrals[$referralUser->referralFrom->name] = 1;
                }
            }
        }
        // foreach($users as $user) {
        //     $invitedCount = $user->referrals()->where('referral_tender_id', $referral->id)->count();
        //     if ($invitedCount > 0)
        //         $topReferrals[$user->name] = $invitedCount;
        // }
        array_multisort($topReferrals, SORT_DESC);
        return view('admin.referral.edit', compact('referral', 'topReferrals'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ReferralTender  $referral
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReferralTender $referral)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'ru_description' => 'required|string',
            'uz_description' => 'required|string',
            'total_pot' => 'required|integer'
        ]);
        $data = $request->all();
        $data['ru_description'] = str_replace('&nbsp;', ' ', $data['ru_description']);
        $data['uz_description'] = str_replace('&nbsp;', ' ', $data['uz_description']);
        $referral->update($data);
        $referral->levels()->delete();
        foreach ($request->get('levels') as $level) {
            $referral->levels()->create($level);
        }
        $image = $request->file('image_ru');
        if ($image) {
            File::delete($referral->image_ru);
            $referral->image_ru = $this->saveImage($image);
            $referral->save();
        }
        $image = $request->file('image_uz');
        if ($image) {
            File::delete($referral->image_uz);
            $referral->image_uz = $this->saveImage($image);
            $referral->save();
        }
        return redirect()->route('admin.referral.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ReferralTender $referral
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(ReferralTender $referral)
    {
        $referral->levels()->delete();
        $referral->delete();
        return redirect()->route('admin.referral.index');
    }

    private function saveImage(UploadedFile $image)
    {
        $filename = Str::random() . '.' . $image->getClientOriginalExtension();
        $filepath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . $filename;
        Storage::disk('public')->put($filename, File::get($image));
        return $filepath;
    }
}
