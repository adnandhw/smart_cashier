@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Daftar Kasir & Pengguna')

@section('content')
<div class="space-y-6" x-data="{ addModalOpen: {{ $errors->any() && !old('_method') ? 'true' : 'false' }}, editModalOpen: {{ $errors->any() && old('_method') == 'PUT' ? 'true' : 'false' }}, currentUser: {} }">
    
    <!-- Top Action Header bar -->
    <div class="glass-card rounded-[22px] p-4 flex flex-row items-center justify-between shadow-sm">
        <div class="text-xs font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider">
            Pengelolaan Akun Kasir Sistem POS
        </div>
        <button type="button" 
                @click="addModalOpen = true" 
                class="px-4.5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-500/10 hover:shadow-amber-500/25 transition-all flex items-center gap-2 cursor-pointer">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Akun Kasir
        </button>
    </div>

    <!-- Users Table list card -->
    <div class="glass-card rounded-[24px] p-6 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Inisial</th>
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Alamat Email</th>
                        <th class="py-3 px-4 text-center">Jabatan</th>
                        <th class="py-3 px-4 text-center">Terdaftar Tanggal</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/60 dark:divide-slate-800/40 text-xs text-slate-700 dark:text-slate-300">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                            <td class="py-3.5 px-4 shrink-0">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold flex items-center justify-center text-xs">
                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="block font-bold text-slate-850 dark:text-white leading-tight">{{ $u->name }}</span>
                                @if(Auth::id() == $u->id)
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[8px] bg-emerald-500/10 text-emerald-600 dark:text-emerald-450 border border-emerald-500/10 uppercase tracking-wide font-extrabold mt-1">Sesi Aktif</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-550 dark:text-slate-400">{{ $u->email }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-500/10 text-amber-700 border border-amber-500/20">Kasir POS</span>
                            </td>
                            <td class="py-3.5 px-4 text-center text-slate-400 dark:text-slate-500 font-semibold">{{ $u->created_at->format('d M Y') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Edit Trigger -->
                                    <button type="button"
                                            data-user="{{ json_encode($u) }}"
                                            @click="currentUser = JSON.parse($el.getAttribute('data-user')); editModalOpen = true"
                                            class="p-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-amber-400 hover:text-white hover:border-amber-400 transition-all duration-200 shadow-xs hover:shadow-md hover:shadow-amber-500/15 cursor-pointer" title="Ubah Akun">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>

                                    @if(Auth::id() != $u->id)
                                        <!-- Delete Trigger -->
                                        <form action="{{ route('users.delete', $u->id) }}" method="POST" onsubmit="confirmUserDelete(event)" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg bg-white border border-slate-200 hover:bg-rose-600 hover:text-white hover:border-rose-600 text-slate-600 transition-all duration-200 shadow-xs hover:shadow-md hover:shadow-rose-600/15 cursor-pointer" title="Hapus Akun">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @else
                                        <!-- Disabled delete button for self -->
                                        <button disabled type="button" class="p-2 rounded-lg bg-slate-50 border border-slate-100 text-slate-350 cursor-not-allowed" title="Anda tidak dapat menghapus diri sendiri">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-5 border-t border-slate-100 dark:border-slate-800/80 pt-4 flex items-center justify-between">
            <span class="text-[11px] text-slate-450 font-semibold uppercase font-display">Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}</span>
            <div class="inline-flex gap-2">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- =========================================================================
     * ADD USER MODAL (Alpine.js controlled)
     * ========================================================================= -->
    <div id="add-user-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-show="addModalOpen" x-transition.opacity style="display: none;">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs" @click="addModalOpen = false"></div>
        <!-- Modal Card -->
        <div class="relative bg-white border border-slate-200 rounded-[28px] max-w-sm w-full shadow-2xl p-6 md:p-8 overflow-hidden transition-all transform scale-100">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <h3 class="text-base font-extrabold text-slate-800">Tambah Akun Kasir</h3>
                <button type="button" @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                @csrf
                <!-- Name -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Kasir" 
                           class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-500 transition-all text-slate-800">
                </div>
                
                <!-- Email -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="budi@kasir.com" 
                           class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-500 transition-all text-slate-800">
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kata Sandi Awal</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter" 
                           class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-500 transition-all text-slate-800 font-mono">
                </div>

                <div class="pt-4 border-t border-slate-100 mt-6 flex justify-end gap-3 shrink-0">
                    <button type="button" @click="addModalOpen = false" class="px-4.5 py-2.5 rounded-xl border border-slate-200 hover:bg-amber-400 hover:text-white hover:border-amber-400 text-xs font-bold text-slate-600 transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4.5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-500/10 transition-all cursor-pointer">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- =========================================================================
     * EDIT USER MODAL (Alpine.js controlled)
     * ========================================================================= -->
    <div id="edit-user-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-show="editModalOpen" x-transition.opacity style="display: none;">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs" @click="editModalOpen = false"></div>
        <!-- Modal Card -->
        <div class="relative bg-white border border-slate-200 rounded-[28px] max-w-sm w-full shadow-2xl p-6 md:p-8 overflow-hidden transition-all transform scale-100">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <h3 class="text-base font-extrabold text-slate-800">Ubah Akun Kasir</h3>
                <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Action route uses dynamic interpolation via Alpine -->
            <form :action="'{{ url('/users') }}/' + currentUser.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <!-- Name -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="name" required :value="currentUser.name"
                           class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-500 transition-all text-slate-800">
                </div>
                
                <!-- Email -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Alamat Email</label>
                    <input type="email" name="email" required :value="currentUser.email"
                           class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-500 transition-all text-slate-800">
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" 
                           class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-3.5 text-xs font-semibold outline-none focus:border-amber-500 transition-all text-slate-800 font-mono">
                </div>

                <div class="pt-4 border-t border-slate-100 mt-6 flex justify-end gap-3 shrink-0">
                    <button type="button" @click="editModalOpen = false" class="px-4.5 py-2.5 rounded-xl border border-slate-200 hover:bg-amber-400 hover:text-white hover:border-amber-400 text-xs font-bold text-slate-600 transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4.5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white rounded-xl text-xs font-bold shadow-md shadow-amber-500/10 transition-all cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    // Alert users before delete action executes
    function confirmUserDelete(e) {
        e.preventDefault();
        const form = e.target.closest ? e.target.closest('form') : e.target;
        if (!form) return;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Akun Kasir?',
                text: "Pengguna terpilih tidak akan dapat masuk kembali ke sistem POS!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48', // rose 600
                cancelButtonColor: '#64748b', // slate 500
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl border border-slate-100 bg-white shadow-2xl',
                    title: 'font-display font-bold text-slate-800',
                    htmlContainer: 'text-xs text-slate-500 font-semibold'
                }
            }).then((result) => {
                if (result && result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if (confirm("Apakah Anda yakin ingin menghapus akun kasir ini?")) {
                form.submit();
            }
        }
    }
</script>
@endsection

