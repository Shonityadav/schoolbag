@extends('layouts.student')
@section('title', 'My Profile')
@section('nav_profile', 'active')

@push('styles')
<style>
/* Override default layout body background if possible, or just cover it */
.profile-container {
    position: relative;
    width: 100%;
    min-height: 100vh;
    /* background-color: #FDF6E9;  */
    z-index: 1;
    overflow-x: hidden;
    padding-bottom: 120px;
}
.content{ padding: 0px;}

/* Wavy Top Background */
.profile-top-wave {
    position: absolute;
    top: 0;
    left: -105px;
    width: 160%;
    height: 300px;
    background-image: url('{{ asset("uploads/images/banners/shapes.png") }}');
    background-size: cover;
    background-position: center bottom;
    border-bottom-left-radius: 50% 20%;
    border-bottom-right-radius: 50% 20%;
    z-index: -1;
}
.profile-top-wave::after {
    content: '';
    position: absolute;
    bottom: -30px;
    left: -10%;
    width: 120%;
    height: 150px;
    background: rgba(255, 206, 99, 0.4);
    border-top-left-radius: 50% 100%;
    border-top-right-radius: 50% 100%;
    border-bottom-left-radius: 50% 20%;
    border-bottom-right-radius: 50% 20%;
    z-index: -1;
    transform: rotate(-3deg);
}



/* Header & Back Button */
.profile-header-bar {
    padding: 20px;
    display: flex;
    align-items: center;
}

/* Avatar Section */
.profile-avatar-wrapper {
    position: relative;
    width: 180px;
    height: 180px;
    margin: 0 auto 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.profile-avatar-frame {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 6;
    pointer-events: none;
}
.profile-avatar-img {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    object-fit: cover;
    background-size: cover;
    background-position: center;
    z-index: 5;
}
.edit-pencil {
    position: absolute;
    bottom: 15px;
    right: 25px;
    width: 32px;
    height: 32px;
    background: white;
    border: 1px solid #CCC;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    font-size: 16px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    cursor: pointer;
}

/* User Info */
.profile-info {
    text-align: center;
    margin-bottom: 30px;
    position: relative;
    z-index: 2;
}
.profile-name {
    font-size: 28px;
    font-weight: 900;
    color: #332211;
    margin-bottom: 0;
}
.profile-email {
    font-size: 14px;
    color: #554433;
    margin-bottom: 8px;
    font-weight: 600;
}
.profile-class-level {
    font-size: 16px;
    font-weight: 700;
    color: #554433;
}

/* Stats Cards */
.stats-container {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-bottom: 40px;
    position: relative;
    z-index: 2;
    padding: 0 20px;
}
.stat-card {
    background: #FFEAC2;
    border: 2px solid #5B3B24;
    border-radius: 12px;
    width: 100px;
    height: 110px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: 0 4px 0 #D4B688;
}
/* Calendar pins */
.stat-card::before, .stat-card::after {
    content: '';
    position: absolute;
    top: -8px;
    width: 10px;
    height: 16px;
    background: #5B3B24;
    border-radius: 4px;
}
.stat-card::before { left: 16px; }
.stat-card::after { right: 16px; }
.stat-icon {
    font-size: 32px;
    margin-bottom: 4px;
    margin-top: 8px;
}
.stat-icon img {
    width: 36px;
    height: 36px;
    object-fit: contain;
}
.stat-value {
    font-size: 14px;
    font-weight: 800;
    color: #5B3B24;
}

/* Profile Menu Dropdowns */
.profile-dropdown-btn {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #FFEAC2;
    border: none;
    border-radius: 999px;
    padding: 16px 24px;
    margin-bottom: 16px;
    font-size: 17px;
    font-weight: 800;
    color: #1E1E35;
    text-decoration: none;
    box-shadow: 0 4px 0 #E5C384, 0 4px 12px rgba(0,0,0,0.05);
    width: 100%;
    transition: transform 0.1s, box-shadow 0.1s;
}
.profile-dropdown-btn:hover {
    color: #1E1E35;
    text-decoration: none;
}
.profile-dropdown-btn:active {
    transform: translateY(4px);
    box-shadow: 0 0 0 #E5C384;
}
.profile-dropdown-btn svg {
    width: 22px;
    height: 22px;
    color: #1E1E35;
    stroke-width: 3;
}

/* Logout Button */
.profile-logout-btn {
    background: #FF6B74;
    color: white;
    font-family: 'Quicksand', sans-serif;
    font-weight: 900;
    font-size: 18px;
    border: none;
    border-radius: 999px;
    padding: 12px 48px;
    box-shadow: 0 5px 0 #E55760;
    transition: transform 0.1s, box-shadow 0.1s;
    cursor: pointer;
    display: inline-block;
}
.profile-logout-btn:active {
    transform: translateY(5px);
    box-shadow: 0 0 0 #E55760;
}

/* Personal Info Accordion */
.pic-container {
    background: #FFEAC2;
    border: 3px solid #FFEAC2;
    border-radius: 24px;
    margin-bottom: 16px;
    box-shadow: 0 4px 0 #E5C384, 0 4px 12px rgba(0,0,0,0.05);
    transition: all 0.2s ease;
}
.pic-container.is-expanded {
    background: #FFF2D1; 
    border-color: #F3E1B6; 
    box-shadow: none;
}

.pic-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    cursor: pointer;
    text-decoration: none;
    position: relative;
}
.pic-container.is-expanded .pic-header {
    justify-content: center;
    padding: 12px 24px;
}

.pic-title {
    font-size: 17px;
    font-weight: 800;
    color: #1E1E35;
    background: transparent;
    padding: 0;
    border-radius: 999px;
    transition: all 0.2s;
}
.pic-container.is-expanded .pic-title {
    font-size: 15px;
    background: #FFD680;
    padding: 6px 24px;
}

.pic-icon {
    width: 22px; 
    height: 22px; 
    color: #1E1E35; 
    stroke-width: 3;
    transition: all 0.2s;
}
.pic-container.is-expanded .pic-icon {
    position: absolute;
    right: 24px;
    top: 50%;
    margin-top: -11px;
    transform: rotate(-90deg);
}

/* Form fields */
.pic-field-row {
    display: flex;
    align-items: flex-end;
    margin-bottom: 12px;
    font-size: 15px;
    font-weight: 800;
    color: #1E1E35;
}
.pic-field-label {
    white-space: nowrap;
    margin-right: 8px;
}
.pic-field-value {
    flex-grow: 1;
    border-bottom: 2px dashed #1E1E35;
    min-height: 20px;
    line-height: 1.2;
    padding-bottom: 2px;
    text-align: center;
    color: #5B3B24;
}


/* Ensure the layout sidebar is above */
.sidebar {
    z-index: 1000 !important;
}

/* Hide topbar on this page */
.topbar {
    display: none !important;
}

/* ── Tablet / iPad responsive ── */
@media (min-width: 600px) {
    .profile-container {
        max-width: 900px;
        margin: 0 auto;
        padding-bottom: 180px;
    }
    .profile-top-wave {
        left: 50%;
        transform: translateX(-50%);
        width: 700px;
        height: 380px;
    }
    .profile-avatar-wrapper {
        width: 250px;
        height: 250px;
        margin-bottom: 16px;
    }
    .profile-avatar-img {
        width: 190px;
        height: 190px;
    }
    .edit-pencil {
        width: 44px;
        height: 44px;
        font-size: 22px;
        bottom: 20px;
        right: 30px;
    }
    .profile-name {
        font-size: 38px;
    }
    .profile-email {
        font-size: 18px;
    }
    .profile-class-level {
        font-size: 20px;
    }
    .stat-card {
        width: 140px;
        height: 155px;
    }
    .stat-icon {
        font-size: 44px;
    }
    .stat-icon img {
        width: 52px;
        height: 52px;
    }
    .stat-value {
        font-size: 18px;
    }
    .profile-menu-section {
        max-width: 100% !important;
        margin: 0 auto !important;
        padding: 0 30px !important;
        margin-top: 40px !important;
    }
    .profile-dropdown-btn {
        font-size: 22px;
        padding: 20px 30px;
        margin-bottom: 20px;
    }
    .pic-title {
        font-size: 22px;
    }
    .profile-logout-btn {
        font-size: 24px;
        padding: 16px 64px;
    }
    .stats-container {
        gap: 24px;
        margin-bottom: 50px;
    }
}
</style>
@endpush

@section('content')
<div class="profile-container">
    <div class="profile-top-wave"></div>

    <div class="profile-header-bar">
        <a href="{{ route('student.dashboard') }}" style="display: inline-block; transition: transform 0.1s;">
            <img src="{{ asset('uploads/images/buttons/Previous button.png') }}" alt="Back" style="height: 48px; object-fit: contain;" fetchpriority="high" loading="eager" decoding="async">
        </a>
    </div>

    <div class="profile-avatar-wrapper">
        @if($user->avatar)
            <img src="{{ asset($user->avatar) }}" 
                 class="profile-avatar-img" alt="Avatar" fetchpriority="high" loading="eager" decoding="async">
        @else
            <div class="profile-avatar-img d-flex align-items-center justify-content-center" style="background: {{ $user->initials_bg }}; color: #FFFFFF; font-size: 56px; font-weight: bold; z-index: 5; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
                {{ $user->initials }}
            </div>
        @endif
        <img src="{{ $user->banner ? asset($user->banner) : asset('uploads/images/banners/frames/Bronze.png') }}" 
             class="profile-avatar-frame" alt="Frame" fetchpriority="high" loading="eager" decoding="async">
        <div class="edit-pencil" data-bs-toggle="modal" data-bs-target="#editProfileModal">
            ✏️
        </div>
    </div>

    <div class="profile-info">
        <h2 class="profile-name">{{ $user->name }}</h2>
        <div class="profile-email">{{ $user->email }}</div>
        <div class="profile-class-level">
            @if($user->user_type == 1)
                Admin &bull; Level {{ $user->level ?? 1 }}
            @elseif($user->user_type == 2)
                Staff &bull; Level {{ $user->level ?? 1 }}
            @else
                Class {{ $user->studentClass?->standard ?? 'N/A' }} &bull; Level {{ $user->level ?? 1 }}
            @endif
        </div>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon" style="color: #FFB300;">⭐</div>
            <div class="stat-value">{{ number_format($user->total_xp ?? 320) }} XP</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color: #FF5722;">🔥</div>
            <div class="stat-value">{{ $user->streak_count ?? 2 }} Streak</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <img src="{{ asset('uploads/images/banners/badges/Gold.png') }}" alt="Gold Tier" fetchpriority="high" loading="eager" decoding="async">
            </div>
            <div class="stat-value">Gold Tier</div>
        </div>
    </div>

    <div class="profile-menu-section" style="padding: 0 30px; max-width: 500px; margin: 0 auto; margin-top: 30px; position: relative; z-index: 2;">
        
        <div class="pic-container" id="personalInfoCard">
            <a href="javascript:void(0);" id="personalInfoToggle" class="pic-header collapsed">
              <span class="pic-title">Personal Information</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="pic-icon">
                  <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
          </a>
            <div id="personalInfoCollapse" class="collapse">
                <div class="px-4 pb-4 pt-3">
                    <div class="d-flex gap-3 mb-3">
                        <div style="width: 100px; height: 120px; background: #FFC145; border-radius: 12px; flex-shrink: 0;"></div>
                        <div style="flex-grow: 1; font-weight: 800; color: #1E1E35; font-size: 14px; line-height: 2; display: flex; flex-direction: column; justify-content: space-between;">
                            <div class="d-flex align-items-end"><span style="width: 65px;">Name -</span> <span class="flex-grow-1 ms-1 text-center" style="border-bottom: 1.5px dashed #1E1E35; padding-bottom: 2px;">{{ $user->name ?? '' }}</span></div>
                            <div class="d-flex align-items-end"><span style="width: 65px;">Class -</span> <span class="flex-grow-1 ms-1 text-center" style="border-bottom: 1.5px dashed #1E1E35; padding-bottom: 2px;">{{ $user->studentClass?->standard ?? '' }}</span></div>
                            <div class="d-flex align-items-end"><span style="width: 65px;">Roll No. -</span> <span class="flex-grow-1 ms-1 text-center" style="border-bottom: 1.5px dashed #1E1E35; padding-bottom: 2px;">{{ $user->roll_no ?? '' }}</span></div>
                            <div class="d-flex align-items-end"><span style="width: 65px;">D.O.B. -</span> <span class="flex-grow-1 ms-1 text-center" style="border-bottom: 1.5px dashed #1E1E35; padding-bottom: 2px;">{{ $user->dob ?? '' }}</span></div>
                        </div>
                    </div>
                    <div style="font-weight: 800; color: #1E1E35; font-size: 14px; line-height: 2;">
                        <div class="d-flex align-items-end"><span style="white-space: nowrap;">School Name -</span> <span class="flex-grow-1 ms-2 text-center" style="border-bottom: 1.5px dashed #1E1E35; padding-bottom: 2px;">{{ $user->institute->name ?? '' }}</span></div>
                        <div class="w-100 mt-2" style="border-bottom: 1.5px dashed #1E1E35; height: 16px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('student.profile.change_password') }}" class="profile-dropdown-btn">
            <span>Change Password</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>

        <a href="{{ route('student.terms') }}" target="_self" class="profile-dropdown-btn">
            <span>Terms and conditions</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>

        <a href="{{ route('student.privacy') }}" target="_self" class="profile-dropdown-btn">
            <span>Privacy Policy</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>

        <a href="{{ route('student.profile.feedback') }}" class="profile-dropdown-btn">
            <span>Feedback</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>

        <a href="#" id="dataDeletionBtn" class="profile-dropdown-btn text-danger">
            <span>Data Deletion Request</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        
        <div class="mt-4 pb-4 px-4 text-center">
            <button type="button" class="profile-logout-btn" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                Log out
            </button>
        </div>
    </div>

</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 380px; width: 95%;">
    <div class="modal-content ep-modal-content">

      <!-- Back & Close buttons row -->
      <div class="ep-top-bar">
        <button type="button" data-bs-dismiss="modal" class="ep-icon-btn" aria-label="Close">
          <img src="{{ asset('uploads/images/buttons/Previous button.png') }}" alt="Back" style="height: 48px; object-fit: contain;" fetchpriority="high" loading="eager" decoding="async">
        </button>
        <button type="button" data-bs-dismiss="modal" class="ep-icon-btn" aria-label="Close">
          <img src="{{ asset('uploads/images/buttons/cross button.png') }}" alt="Close" style="height: 48px; object-fit: contain;" fetchpriority="high" loading="eager" decoding="async">
        </button>
      </div>

      <!-- Your Profile Banner -->
      <div class="ep-banner-wrap">
        <img src="{{ asset('uploads/images/edit_profile/banner.png') }}" alt="Banner Background" class="ep-banner-img" fetchpriority="high" loading="eager" decoding="async">
        <h3 class="ep-banner-text">Your Profile</h3>
      </div>

      <!-- Current Avatar Preview -->
      <div class="ep-avatar-preview-wrap">
        <div class="ep-avatar-preview">
          @if($user->avatar)
            <img id="ep-preview-avatar" src="{{ asset($user->avatar) }}" alt="Avatar" class="ep-preview-avatar-img">
            <div id="ep-preview-initials" class="ep-preview-avatar-img d-none align-items-center justify-content-center" style="background: {{ $user->initials_bg }}; color: #FFFFFF; font-size: 40px; font-weight: bold; z-index: 1; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
              {{ $user->initials }}
            </div>
          @else
            <img id="ep-preview-avatar" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" alt="Avatar" class="ep-preview-avatar-img d-none" fetchpriority="high" loading="eager" decoding="async">
            <div id="ep-preview-initials" class="ep-preview-avatar-img d-flex align-items-center justify-content-center" style="background: {{ $user->initials_bg }}; color: #FFFFFF; font-size: 40px; font-weight: bold; z-index: 1; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
              {{ $user->initials }}
            </div>
          @endif
          <img id="ep-preview-frame" src="{{ $user->banner ? asset($user->banner) : asset('uploads/images/banners/frames/Bronze.png') }}" alt="Frame" class="ep-preview-frame-img">
        </div>
      </div>

      <!-- Tab Buttons -->
      <div class="ep-tabs">
        <button class="ep-tab active" id="ep-tab-avatars" onclick="epSwitchTab('avatars')">Avatars</button>
        <button class="ep-tab" id="ep-tab-frames" onclick="epSwitchTab('frames')">Frames</button>
      </div>

      <!-- Form -->
      <form action="{{ route('student.profile.update_avatar') }}" method="POST" id="epForm">
        @csrf

        <!-- Background Wrapper for Grids -->
        <div class="ep-bg-wrapper">

        <!-- Avatars Grid -->
        <div id="ep-avatars-grid" class="ep-grid-scroll">
          <div class="ep-grid">
          @php
             // $avatars and $unlockedAvatars are provided by the controller
          @endphp
          @foreach($avatars as $i => $av)
            @php $isUnlocked = in_array($av, $unlockedAvatars); $path = 'uploads/images/banners/Avatar/'.$av; @endphp
            <label class="ep-cell {{ !$isUnlocked ? 'ep-locked' : '' }}">
              @if($isUnlocked)
                <input type="radio" name="avatar" value="{{ $path }}" class="d-none ep-radio" data-preview="avatar"
                  {{ ($user->avatar == $path) ? 'checked' : '' }}>
                <img src="{{ asset($path) }}" alt="Avatar" class="ep-cell-img ep-selectable" onclick="epUpdatePreview('avatar', '{{ asset($path) }}')" fetchpriority="high" loading="eager" decoding="async">
              @else
                <img src="{{ asset('uploads/images/buttons/lock button.png') }}" alt="Locked" class="ep-lock-img" fetchpriority="high" loading="eager" decoding="async">
              @endif
            </label>
          @endforeach
          </div><!-- /.ep-grid -->
        </div><!-- /.ep-grid-scroll -->

        <!-- Frames Grid -->
        <div id="ep-frames-grid" class="ep-grid-scroll" style="display: none;">
          <div class="ep-grid">
          @php
             // $banners and $unlockedFrames are provided by the controller
          @endphp
          @foreach($banners as $fr)
            @php $isUnlocked = in_array($fr, $unlockedFrames); $fpath = 'uploads/images/banners/frames/'.$fr; @endphp
            <label class="ep-cell {{ !$isUnlocked ? 'ep-locked' : '' }}">
              @if($isUnlocked)
                <input type="radio" name="banner" value="{{ $fpath }}" class="d-none ep-radio" data-preview="frame"
                  {{ ($user->banner == $fpath) ? 'checked' : '' }}>
                <img src="{{ asset($fpath) }}" alt="Frame" class="ep-cell-img ep-selectable" onclick="epUpdatePreview('frame', '{{ asset($fpath) }}')" fetchpriority="high" loading="eager" decoding="async">
              @else
                <img src="{{ asset('uploads/images/buttons/lock button.png') }}" alt="Locked" class="ep-lock-img" fetchpriority="high" loading="eager" decoding="async">
              @endif
            </label>
          @endforeach
          </div><!-- /.ep-grid -->
        </div><!-- /.ep-grid-scroll -->

        </div><!-- /.ep-bg-wrapper -->

        <!-- Save Button -->
        <div class="ep-save-wrap">
          <button type="submit" class="ep-save-btn">
            <img src="{{ asset('uploads/images/edit_profile/save.png') }}" alt="Save Changes" class="ep-save-img" fetchpriority="high" loading="eager" decoding="async">
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 360px; width: 90%; margin: auto;">
    <div class="modal-content" style="border-radius: 36px; border: none; box-shadow: 0 16px 48px rgba(0,0,0,0.2); overflow: visible; background: transparent;">
      
      <!-- Mascot sticking out of the top right -->
      <img src="{{ asset('uploads/images/log out/5.png') }}" style="position: absolute; top: -60px; right: -40px; width: 140px; height: 140px; z-index: 10; object-fit: contain; pointer-events: none; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.15));" alt="Sad Mascot">

      <!-- Modal Body -->
      <div style="background: linear-gradient(to bottom, #FA6A7E 0%, #FA6A7E 50%, #FBF1DC 50%, #FBF1DC 100%); border-radius: 36px; position: relative; overflow: hidden; display: flex; flex-direction: column;">
        
        <!-- Top Half (Pink) -->
        <div style="padding: 35px 20px 20px; text-align: center; color: white;">
          <!-- Blue Arrow Icon Image -->
          <img src="{{ asset('uploads/images/log out/3.png') }}" style="width: 72px; height: 72px; object-fit: contain; margin: 0 auto 12px; display: block; filter: drop-shadow(0 8px 16px rgba(0,0,0,0.15));" alt="Log Out Arrow">
          <h4 style="font-family: 'Bubblegum Sans', cursive; margin: 0; font-size: 34px; letter-spacing: 1.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">Log Out</h4>
        </div>
        
        <!-- Bottom Half (Cream) -->
        <div style="padding: 10px 24px 28px; text-align: center; position: relative;">
          <!-- Stars decoration (3D Faceted) -->
          <svg style="position: absolute; left: -12px; top: 12px; width: 44px; height: 44px; filter: drop-shadow(0 6px 8px rgba(0,0,0,0.25)); z-index: 1;" viewBox="0 0 100 100">
            <defs>
              <linearGradient id="yellowStar3D" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#FFF394" />
                <stop offset="40%" stop-color="#FFD52E" />
                <stop offset="100%" stop-color="#DF8F00" />
              </linearGradient>
            </defs>
            <polygon points="50,5 61,39 97,39 68,60 79,94 50,73 21,94 32,60 3,39 39,39" fill="url(#yellowStar3D)" stroke="#FFE76B" stroke-width="2" stroke-linejoin="round"></polygon>
            <path d="M50,5 L50,53 M97,39 L50,53 M79,94 L50,53 M21,94 L50,53 M3,39 L50,53" stroke="rgba(255,255,255,0.6)" stroke-width="2" stroke-linecap="round" fill="none" />
            <path d="M61,39 L50,53 M68,60 L50,53 M32,60 L50,53 M39,39 L50,53 M50,73 L50,53" stroke="rgba(200,120,0,0.35)" stroke-width="2" stroke-linecap="round" fill="none" />
          </svg>
          
          <svg style="position: absolute; left: -2px; top: 50px; width: 34px; height: 34px; filter: drop-shadow(0 6px 8px rgba(0,0,0,0.25)); z-index: 2;" viewBox="0 0 100 100">
            <defs>
              <linearGradient id="blueStar3D" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#8FE0FF" />
                <stop offset="40%" stop-color="#2DA5FF" />
                <stop offset="100%" stop-color="#005BB5" />
              </linearGradient>
            </defs>
            <polygon points="50,5 61,39 97,39 68,60 79,94 50,73 21,94 32,60 3,39 39,39" fill="url(#blueStar3D)" stroke="#BEEBFF" stroke-width="2" stroke-linejoin="round"></polygon>
            <path d="M50,5 L50,53 M97,39 L50,53 M79,94 L50,53 M21,94 L50,53 M3,39 L50,53" stroke="rgba(255,255,255,0.6)" stroke-width="2" stroke-linecap="round" fill="none" />
            <path d="M61,39 L50,53 M68,60 L50,53 M32,60 L50,53 M39,39 L50,53 M50,73 L50,53" stroke="rgba(0,60,150,0.35)" stroke-width="2" stroke-linecap="round" fill="none" />
          </svg>

          <!-- Text Box -->
          <div style="background: #FFF9EF; border-radius: 18px; padding: 22px 16px; margin-bottom: 24px; box-shadow: inset 0 2px 6px rgba(0,0,0,0.03); border: 2px solid #EAE0CD;">
            <p style="color: #1A365D; font-weight: 800; margin: 0; font-size: 16px; line-height: 1.5;">Are you sure you want to<br>log out of your account?</p>
          </div>
          
          <form action="{{ route('student.logout') }}" method="POST" class="m-0 p-0">
              @csrf
              <div style="display: flex; gap: 16px; justify-content: center;">
                  <button type="button" class="btn btn-ghost" data-bs-dismiss="modal" style="flex: 1; border: 2px solid #EAE0CD; color: #1A365D; border-radius: 24px; font-weight: 800; font-size: 16px; padding: 12px 0; display: flex; align-items: center; justify-content: center; text-align: center; m-0">Cancel</button>
                  <button type="submit" class="btn btn-primary" style="flex: 1; background: linear-gradient(135deg, #FF6B80, #F35368); border: none; border-radius: 24px; font-weight: 800; font-size: 16px; padding: 12px 0; box-shadow: 0 6px 16px rgba(243,83,104,0.3); color: white; display: flex; align-items: center; justify-content: center; text-align: center; m-0">Log Out</button>
              </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const logoutModal = document.getElementById('logoutConfirmModal');
        let logoutThoughtInterval;
        let isLogoutModalOpen = false;

        if(logoutModal) {
            const stopLogoutThoughts = () => {
                isLogoutModalOpen = false;
                if(logoutThoughtInterval) {
                    clearInterval(logoutThoughtInterval);
                    logoutThoughtInterval = null;
                }
                if(window.mascotRestore) window.mascotRestore();
            };

            logoutModal.addEventListener('show.bs.modal', function () {
                isLogoutModalOpen = true;
                const sadSvg = "<svg width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' style='vertical-align: sub; margin-left: 2px;'><circle cx='12' cy='12' r='10'></circle><path d='M16 16s-1.5-2-4-2-4 2-4 2'></path><line x1='9' y1='9' x2='9.01' y2='9'></line><line x1='15' y1='9' x2='15.01' y2='9'></line></svg>";
                const thoughts = [
                    "Are you leaving? Please don't go!",
                    "We were having so much fun...",
                    "Aww, leaving so soon?",
                    "I'll miss you! Come back soon!",
                    "Wait! Are you sure?"
                ];
                
                let lastIndex = -1;
                const showNextThought = () => {
                    if (!isLogoutModalOpen) return; // Safety check!
                    let nextIndex = Math.floor(Math.random() * thoughts.length);
                    // Prevent showing the exact same thought twice in a row
                    if (nextIndex === lastIndex) {
                        nextIndex = (nextIndex + 1) % thoughts.length;
                    }
                    lastIndex = nextIndex;
                    const randomThought = thoughts[nextIndex] + " " + sadSvg;
                    if(window.mascotSad) window.mascotSad(randomThought);
                };
                
                // Clear any existing interval just in case
                if(logoutThoughtInterval) clearInterval(logoutThoughtInterval);
                
                showNextThought(); // Initial thought
                logoutThoughtInterval = setInterval(showNextThought, 4000); // Switch every 4 seconds
            });
            
            logoutModal.addEventListener('hide.bs.modal', stopLogoutThoughts);
            logoutModal.addEventListener('hidden.bs.modal', stopLogoutThoughts);
            
            // Also bind explicitly to the Cancel button to guarantee it fires immediately!
            const cancelBtn = logoutModal.querySelector('.btn-ghost[data-bs-dismiss="modal"]');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', stopLogoutThoughts);
            }
        }
        
        // Add thoughts for the profile menu buttons
        const profileLinks = [
            { selector: 'a[href*="change_password"]', text: "Keep your account secure! 🗝️", celebrate: false },
            { selector: 'a[href*="terms"]', text: "Reading the rules is smart! 📜", celebrate: false },
            { selector: 'a[href*="privacy"]', text: "We keep your data super safe! 🛡️", celebrate: false },
            { selector: 'a[href*="feedback"]', text: "We'd love to hear your thoughts! 💭", celebrate: true },
            { selector: '#dataDeletionBtn', text: "Oh no! Deleting data is forever! ⚠️", sad: true, isAlert: true }
        ];


        profileLinks.forEach(item => {
            const el = document.querySelector(item.selector);
            if (el) {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    if (item.sad && window.mascotSad) {
                        window.mascotSad(item.text);
                    } else if (item.celebrate && window.mascotCelebrate) {
                        window.mascotCelebrate(item.text);
                    } else if (window.mascotSay) {
                        window.mascotSay(item.text);
                    }
                    
                    if (item.isAlert) {
                        setTimeout(() => {
                            alert('To request data deletion, please contact your school administrator or email support@schoolbag.in');
                            if(window.mascotRestore) window.mascotRestore();
                        }, 2000);
                    } else {
                        const href = el.getAttribute('href');
                        setTimeout(() => {
                            window.location.href = href;
                        }, 1800); // Wait 1.8s then navigate
                    }
                });
            }
        });
    });
</script>
@endpush

<style>
/* Override Bootstrap's modal-content white bg */
#editProfileModal .modal-content,
#editProfileModal .ep-modal-content {
  background-color: #ffffff !important;
  border: 6px solid #1B74F3 !important;
  border-radius: 28px !important;
  padding-bottom: 20px !important;
  overflow: visible !important;
  left: 4%;
}
.ep-top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 12px 0;
}
.ep-icon-btn {
  background: transparent;
  border: none;
  padding: 0;
  cursor: pointer;
}
.ep-banner-wrap {
  position: absolute;
  top: -60px;
  left: 50%;
  transform: translateX(-50%);
  width: 90%;
  text-align: center;
  z-index: 10;
  display: flex;
  justify-content: center;
  align-items: center;
}
.ep-banner-img {
  width: 100%;
  max-width: 290px;
  height: auto;
  object-fit: contain;
  display: block;
}
.ep-banner-text {
  position: absolute;
  top: 42%;
  left: 50%;
  transform: translate(-50%, -50%);
  margin: 0;
  font-size: 24px;
  font-weight: 800;
  color: #ffffff;
  text-shadow: 2px 2px 0 #000, -2px -2px 0 #000, 2px -2px 0 #000, -2px 2px 0 #000, 0 3px 0 #000;
  letter-spacing: 1px;
  white-space: nowrap;
}
.ep-avatar-preview-wrap {
  display: flex;
  justify-content: center;
  margin-bottom: 12px;
}
.ep-avatar-preview {
  position: relative;
  width: 130px;
  height: 130px;
}
.ep-preview-avatar-img {
  position: absolute;
  inset: 0;
  width: 95px;
  height: 95px;
  margin: auto;
  border-radius: 50%;
  object-fit: cover;
  z-index: 1;
}
.ep-preview-frame-img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
  z-index: 2;
  pointer-events: none;
}
.ep-tabs {
  display: flex;
  justify-content: center;
  gap: 0;
  margin: 0 24px 14px;
  background: #E8F4FF;
  border-radius: 999px;
  padding: 4px;
  border: 2px solid #B8D9FF;
}
.ep-tab {
  flex: 1;
  border: none;
  background: transparent;
  padding: 7px 16px;
  font-family: 'Quicksand', sans-serif;
  font-weight: 800;
  font-size: 14px;
  color: #3B8FE8;
  border-radius: 999px;
  cursor: pointer;
  transition: all 0.2s;
}
.ep-tab.active {
  background-image: url('{{ asset("uploads/images/edit_profile/text banner.png") }}');
  background-size: 100% 100%;
  background-position: center;
  background-repeat: no-repeat;
  background-color: transparent !important;
  color: white;
}
.ep-bg-wrapper {
  margin: 0 auto 16px auto;
  width: 95%;
  padding: 10px; /* Reduced padding since image is removed */
}
.ep-grid-scroll {
  max-height: 220px;
  overflow-y: auto;
  overflow-x: hidden;
  margin: 0 auto;
  width: 100%;
  padding: 0 4px;
}
.ep-grid-scroll::-webkit-scrollbar {
  width: 6px;
}
.ep-grid-scroll::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.4);
  border-radius: 10px;
}
.ep-grid-scroll::-webkit-scrollbar-thumb {
  background: #3B8FE8;
  border-radius: 10px;
}
.ep-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  padding: 0 8px;
}
.ep-cell {
  aspect-ratio: 1;
  background: rgba(255,255,255,0.75);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border: 3px solid #D4B0FF;
  box-shadow: 0 3px 0 #B08FDD;
  transition: border-color 0.15s, transform 0.1s;
  overflow: hidden;
}
.ep-cell:active { transform: translateY(2px); box-shadow: 0 1px 0 #B08FDD; }
.ep-cell:has(.ep-radio:checked) {
  border-color: #3B8FE8;
  box-shadow: 0 3px 0 #1A6FC8;
  background: #E8F4FF;
}
.ep-cell.ep-locked {
  background: rgba(255,255,255,0.5);
  cursor: default;
  border-color: #C8B0E8;
}
.ep-cell-img {
  width: 88%;
  height: 88%;
  object-fit: contain;
  border-radius: 10px;
}
.ep-lock-img {
  width: 55%;
  height: 55%;
  object-fit: contain;
}
.ep-save-wrap {
  text-align: center;
  padding: 0 18px;
}
.ep-save-btn {
  background: transparent;
  border: none;
  padding: 0;
  width: 100%;
  cursor: pointer;
  transition: transform 0.1s;
}
.ep-save-btn:active { transform: translateY(3px); }
.ep-save-img {
  width: 100%;
  max-width: 290px;
  height: auto;
  object-fit: contain;
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const personalInfoCard = document.getElementById('personalInfoCard');
    const personalInfoToggle = document.getElementById('personalInfoToggle');
    const personalInfoCollapse = document.getElementById('personalInfoCollapse');

    if (!personalInfoCard || !personalInfoToggle || !personalInfoCollapse) {
        return;
    }

    /*
     * Create one Bootstrap Collapse instance.
     * We control the toggle manually so Bootstrap's
     * automatic click handling cannot conflict with
     * the mascot.
     */
    const collapseInstance = bootstrap.Collapse.getOrCreateInstance(
        personalInfoCollapse,
        {
            toggle: false
        }
    );

    /*
     * Arrow / header click
     */
    personalInfoToggle.addEventListener('click', function (e) {

        e.preventDefault();
        e.stopPropagation();

        /*
         * If currently open -> CLOSE
         * If currently closed -> OPEN
         */
        if (personalInfoCollapse.classList.contains('show')) {
            collapseInstance.hide();
        } else {
            collapseInstance.show();
        }
    });


    /*
     * AFTER the accordion has actually opened.
     * Mascot speaks ONLY here.
     */
    personalInfoCollapse.addEventListener('shown.bs.collapse', function () {

        personalInfoCard.classList.add('is-expanded');
        personalInfoToggle.classList.remove('collapsed');

        if (window.mascotCelebrate) {
            window.mascotCelebrate("Let's check out your profile!");
        }
    });


    /*
     * AFTER the accordion has actually closed.
     * Absolutely no mascot thought here.
     */
    personalInfoCollapse.addEventListener('hidden.bs.collapse', function () {

        personalInfoCard.classList.remove('is-expanded');
        personalInfoToggle.classList.add('collapsed');
    });

});

function epSwitchTab(tab) {
    document.getElementById('ep-avatars-grid').style.display = (tab === 'avatars') ? 'block' : 'none';
    document.getElementById('ep-frames-grid').style.display  = (tab === 'frames')  ? 'block' : 'none';
    document.getElementById('ep-tab-avatars').classList.toggle('active', tab === 'avatars');
    document.getElementById('ep-tab-frames').classList.toggle('active',  tab === 'frames');
}

function epUpdatePreview(type, src) {
    if (type === 'avatar') {
        let avatarImg = document.getElementById('ep-preview-avatar');
        let initialsDiv = document.getElementById('ep-preview-initials');
        if(src) {
            avatarImg.src = src;
            avatarImg.classList.remove('d-none');
            initialsDiv.classList.add('d-none');
            initialsDiv.classList.remove('d-flex');
        }
    } else {
        document.getElementById('ep-preview-frame').src = src;
    }
}

@if(session('chest_unlocked'))
    setTimeout(function() {
        alert("🎉 Chest Opened! You unlocked a new Avatar: {{ session('chest_unlocked') }}");
    }, 500);
@endif
</script>
@endpush
