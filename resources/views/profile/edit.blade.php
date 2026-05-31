@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Pengaturan Akun & Profil')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <div class="bg-white border border-slate-100 rounded-2xl p-6 md:p-8 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-emerald-500 to-teal-500"></div>

        <div class="mb-6">
            <h3 class="font-bold text-slate-800 text-base md:text-lg"><i class="fa-solid fa-user-gear mr-1.5 text-emerald-500"></i>Ubah Informasi Profil</h3>
            <p class="text-xs text-slate-400 mt-0.5">Ubah nama, email, dan konfigurasi hari gajian bulanan Anda.</p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Name & Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Alamat Email</label>
                    <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                </div>
            </div>

            <!-- Payday Day -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanggal Gajian Bulanan</label>
                <p class="text-[10px] text-slate-400 mb-2">Paling lambat tanggal 28/30. Sistem menggunakannya untuk membagi saldo Anda dengan sisa hari hingga gajian untuk menetapkan "Batas Aman Harian".</p>
                <select name="payday_day" required class="w-full bg-slate-50 border border-slate-100 text-slate-700 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                    @for ($i = 1; $i <= 31; $i++)
                        <option value="{{ $i }}" {{ old('payday_day', $user->payday_day) == $i ? 'selected' : '' }}>Tanggal {{ $i }}</option>
                    @endfor
                </select>
            </div>

            <!-- Change Password Divider -->
            <div class="border-t border-slate-50 pt-5 mt-5">
                <h4 class="font-bold text-slate-700 text-sm mb-1.5"><i class="fa-solid fa-key mr-1.5 text-slate-400"></i>Ubah Password (Opsional)</h4>
                <p class="text-[10px] text-slate-400 mb-4">Kosongkan kolom di bawah jika Anda tidak berniat merubah password akun.</p>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Password Saat Ini</label>
                        <input type="password" name="current_password" placeholder="Masukkan password lama" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Password Baru</label>
                            <input type="password" name="new_password" placeholder="Password baru" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" placeholder="Ulangi password baru" class="w-full bg-slate-50 border border-slate-100 text-slate-800 placeholder-slate-400 rounded-xl py-3 px-4 text-xs md:text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs md:text-sm rounded-xl py-3 shadow-md shadow-emerald-100 transition active:scale-95 text-center mt-4">
                Simpan Perubahan Profil
            </button>
        </form>
    </div>

</div>
@endsection
