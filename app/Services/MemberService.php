<?php

namespace App\Services;

use App\Models\Member;
use App\Models\User;
use App\Models\Account;
use App\Notifications\MemberWelcomeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemberService
{
    /**
     * Create a new member with user account and default savings account
     */
    public function createMember(array $data): Member
    {
        return DB::transaction(function () use ($data) {
            // Generate unique SACCO member ID
            $saccoMemberId = $this->generateSaccoMemberId();
            
            // Generate one-time password
            $otp = $this->generateOTP();
            
            // Handle avatar upload
            $avatarPath = null;
            if (isset($data['avatar'])) {
                $avatarPath = $data['avatar']->store('avatars', 'public');
            }
            
            // Create user account
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($otp),
                'must_change_password' => true,
            ]);
            
            // Create member
            $member = Member::create([
                'sacco_member_id' => $saccoMemberId,
                'user_id' => $user->id,
                'name' => $data['name'],
                'date_of_birth' => $data['date_of_birth'],
                'nationality' => $data['nationality'],
                'gender' => $data['gender'],
                'marital_status' => $data['marital_status'],
                'national_id_number' => $data['national_id_number'],
                'passport_number' => $data['passport_number'] ?? null,
                'avatar' => $avatarPath,
                'phone1' => $data['phone1'],
                'phone2' => $data['phone2'] ?? null,
                'district' => $data['district'],
                'county' => $data['county'],
                'sub_county' => $data['sub_county'],
                'village' => $data['village'] ?? null,
                'has_existing_savings' => $data['has_existing_savings'] ?? false,
                'existing_savings_details' => $data['existing_savings_details'] ?? null,
                'is_currently_in_debt' => $data['is_currently_in_debt'] ?? false,
                'debt_details' => $data['debt_details'] ?? null,
            ]);
            
            // Create default savings account
            Account::create([
                'member_id' => $member->id,
                'account_number' => $this->generateAccountNumber($member->id),
                'account_type' => 'savings',
                'balance' => 0,
                'status' => 'active',
            ]);
            
            // Send welcome email with OTP
            $user->notify(new MemberWelcomeNotification($otp, $saccoMemberId));
            
            return $member;
        });
    }

    /**
     * Update an existing member
     */
    public function updateMember(Member $member, array $data): Member
    {
        return DB::transaction(function () use ($member, $data) {
            // Handle avatar upload
            if (isset($data['avatar'])) {
                // Delete old avatar
                if ($member->avatar) {
                    Storage::disk('public')->delete($member->avatar);
                }
                $data['avatar'] = $data['avatar']->store('avatars', 'public');
            }
            
            // Update member
            $member->update([
                'name' => $data['name'],
                'date_of_birth' => $data['date_of_birth'],
                'nationality' => $data['nationality'],
                'gender' => $data['gender'],
                'marital_status' => $data['marital_status'],
                'national_id_number' => $data['national_id_number'],
                'passport_number' => $data['passport_number'] ?? null,
                'avatar' => $data['avatar'] ?? $member->avatar,
                'phone1' => $data['phone1'],
                'phone2' => $data['phone2'] ?? null,
                'district' => $data['district'],
                'county' => $data['county'],
                'sub_county' => $data['sub_county'],
                'village' => $data['village'] ?? null,
                'has_existing_savings' => $data['has_existing_savings'] ?? false,
                'existing_savings_details' => $data['existing_savings_details'] ?? null,
                'is_currently_in_debt' => $data['is_currently_in_debt'] ?? false,
                'debt_details' => $data['debt_details'] ?? null,
            ]);
            
            // Update user email if changed
            if (isset($data['email']) && $member->user->email !== $data['email']) {
                $member->user->update(['email' => $data['email']]);
            }
            
            return $member->fresh();
        });
    }

    /**
     * Generate unique SACCO member ID (BI-SMCL-0001)
     */
    private function generateSaccoMemberId(): string
    {
        $prefix = 'BI-SMCL-';
        $lastMember = Member::latest('id')->first();
        $number = $lastMember ? (int) substr($lastMember->sacco_member_id, -4) + 1 : 1;
        
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate account number for savings account
     */
    private function generateAccountNumber(int $memberId): string
    {
        return 'SAV-' . now()->format('Y') . '-' . str_pad($memberId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate secure one-time password
     */
    private function generateOTP(): string
    {
        return Str::random(8);
    }
}