<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;


class SettingController extends Controller
{
    // Display a listing of settings
    public function index()
    {
        $settings = Setting::all();

        return Inertia::render('Setting/General', [
            'dataSettings' =>  $settings,
        ]);

    }




    public function email()
    {
        $settings = Setting::all();

        return Inertia::render('Setting/Email', [
            'dataSettings' =>  $settings,
        ]);

    }

    public function theme()
    {
        $settings = Setting::all();

        return Inertia::render('Setting/Theme', [
            'dataSettings' =>  $settings,
        ]);
    }


    
    // Show the form for creating a new setting
    public function create()
    {
        return view('settings.create');
    }

    // Store a newly created setting in storage
    public function store(Request $request)
    {
        try {
            $arr = $request->all();
    
            foreach ($arr as $arrKey => $arrValue) {
                // Handle specific cases
                if ($arrKey === 'facebook_admins') {
                    $valueface_admins = array_map('json_encode', $arrValue);
                    $value = json_encode($valueface_admins);
                } elseif ($arrKey === 'analytics_service_account_credentials') {
                    $value = $arrValue;
                } elseif (in_array($arrKey, ['admin-favicon', 'admin-logo', 'admin-login-screen-backgrounds'])) {
                    $value = end($arrValue);
                } else {
                    // For all other keys, including 'admin_email'
                    $value = $arrValue;
                }
    
                // Check if the setting exists
                $isExist = Setting::where('key', $arrKey)->exists();
    
                if ($isExist) {
                    // Update existing setting
                    Setting::where('key', $arrKey)->update([
                        'value' => $value,
                    ]);
                } else {
                    // Create new setting
                    Setting::create([
                        'key'   => $arrKey,
                        'value' => $value,
                    ]);
                }
            }

            $currentData = Setting::all();
    
            return response()->json([
                'message' => 'Settings updated successfully!',
                'currentData' => $currentData,
            ], 200);
    
        } catch (\Exception $e) {
            // Log the exception message

    
            return response()->json([
                'message' => 'An error occurred while updating settings.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    

    // Show the form for editing the specified setting
    public function edit(Setting $setting)
    {
        return view('settings.edit', compact('setting'));
    }

    // Update the specified setting in storage
    public function update(Request $request, Setting $setting)
    {
        $request->validate([
            'key' => 'required|string|unique:settings,key,' . $setting->id,
            'value' => 'nullable|string',
        ]);

        $setting->update($request->all());

        return redirect()->route('settings.index')
            ->with('success', 'Setting updated successfully.');
    }

    // Remove the specified setting from storage
    public function destroy(Setting $setting)
    {
        $setting->delete();

        return redirect()->route('settings.index')
            ->with('success', 'Setting deleted successfully.');
    }
}
