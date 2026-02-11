<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AskDocPH - Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- CSS --}}
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<header class="site-header">
  <div class="header-container">
    
    <!-- LEFT: LOGO -->
    <a href="#login-section" class="logo" id="topLink">
      <img src="{{ asset('images/AskDocPH.png') }}" alt="AskDocPH">
    </a>

    <!-- RIGHT: NAV -->
    <nav class="nav">
      <a href="#about-section" id="aboutLink">About</a>
      <a href="#story-section">Our Story</a>
      <a href="#mission-vision">Mission & Vision</a>
      <a href="#core-values">Values</a>
      <a href="#team-section">Team</a>
      <a href="#cta-section">Get Started</a>
      <a href="/register" class="signup-btn">Sign Up</a>
    </nav>

  </div>
</header>


<div class="auth-shell" id="login-section">
  <!-- LEFT: LOGIN -->
   
  <section class="left">
    <div class="left-inner">
      <h1 class="h-title">Welcome Back</h1>
      <p class="h-sub">Continue your mental wellness journey</p>
      <p class="h-sub" style="margin-bottom: 26px;">Sign in using your email and password</p>

      @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
      @endif

      <form id="loginForm" method="POST" action="{{ route('login.submit') }}">
        @csrf

        <div class="field">
          <label class="label">Email Address</label>
          <div class="input-wrap">
            <svg class="input-ico" viewBox="0 0 24 24" aria-hidden="true">
              <path fill="currentColor" d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/>
            </svg>
            <input id="email" type="email" name="email" placeholder="you@example.com" value="{{ old('email') }}" required>
          </div>
        </div>

        <div class="field">
          <label class="label">Password</label>
          <div class="input-wrap">
            <svg class="input-ico" viewBox="0 0 24 24" aria-hidden="true">
              <path fill="currentColor" d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm6-7h-1V8a5 5 0 0 0-10 0v2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2zm-3 0H9V8a3 3 0 0 1 6 0v2z"/>
            </svg>

            <input id="password" type="password" name="password" placeholder="Enter your password" required>

            <button class="icon-btn" type="button" id="togglePassBtn" aria-label="Toggle password visibility">
              <svg class="input-ico" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="currentColor" d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="row-between">
          <label class="remember">
            <input type="checkbox" name="remember" id="remember">
            Remember me
          </label>

          <a class="link" href="/forgot-password">Forgot password?</a>
        </div>

        <button class="signin-btn" type="submit" id="signInBtn">Sign In</button>

        <div class="bottom">
          New to AskDocPH? <a href="/register">Create an account</a>
        </div>
      </form>

      {{-- OPTIONAL: If you still have old social buttons in HTML somewhere,
           this hidden block shows how JS can remove them if present. --}}
      <div style="display:none;">
        <button class="social-btn" type="button">Continue with Google</button>
        <button class="social-btn" type="button">Continue with Facebook</button>
        <button class="social-btn" type="button">Continue with Apple</button>
        <div class="divider">Or sign in with email</div>
      </div>

    </div>
  </section>

  <!-- RIGHT: INFO PANEL -->
  <section class="right">
    <h2 class="r-title">Your mental health journey<br>starts here</h2>

    <div class="feature">
      <div class="badge" aria-hidden="true">
        <svg viewBox="0 0 24 24">
          <path fill="currentColor" d="M16 11c1.66 0 3-1.34 3-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zM8 11c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.96 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
        </svg>
      </div>
      <div>
        <div class="f-title">Connect with Professionals</div>
        <p class="f-text">Access licensed psychiatrists and mental health experts whenever you need support.</p>
      </div>
    </div>

    <div class="feature">
      <div class="badge" aria-hidden="true">
        <svg viewBox="0 0 24 24">
          <path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-3.33 0-8 1.67-8 5v1h16v-1c0-3.33-4.67-5-8-5z"/>
        </svg>
      </div>
      <div>
        <div class="f-title">Join Our Community</div>
        <p class="f-text">Share experiences and find support from others who understand what you're going through.</p>
      </div>
    </div>

    <div class="feature">
      <div class="badge" aria-hidden="true">
        <svg viewBox="0 0 24 24">
          <path fill="currentColor" d="M12 2 4 5v6c0 5.55 3.84 10.74 8 11 4.16-.26 8-5.45 8-11V5l-8-3zm0 18c-2.91-.5-6-4.64-6-9V6.3L12 4l6 2.3V11c0 4.36-3.09 8.5-6 9z"/>
        </svg>
      </div>
      <div>
        <div class="f-title">Safe &amp; Confidential</div>
        <p class="f-text">Your privacy matters. All conversations are secure and judgment-free.</p>
      </div>
    </div>

    <div class="quote">
      <p>“AskDocPH helped me find the support I needed during my toughest moments. The community is incredible.”</p>
      <span>— Maria S., Manila</span>
    </div>
  </section>
</div>

<!-- ===== ABOUT SECTION ===== -->
<section id="about-section" class="about-section">
  <div class="about-content">
    <h1>About AskDocPH</h1>
    <p>
      We're on a mission to make mental health support accessible, affordable,
      and stigma-free for every Filipino.
    </p>
  </div>
</section>


{{-- JS --}}
<script src="{{ asset('js/login.js') }}"></script>



<!-- ===== OUR STORY SECTION ===== -->
<section id="story-section" class="story-section">
  <div class="story-container">

    <!-- Left: Story -->
    <div class="story-left">
      <div class="story-kicker">MENTAL HEALTH SUPPORT</div>
      <h2 class="story-title">Our Story</h2>

      <p class="story-text">
        AskDocPH was born from a simple observation: too many Filipinos struggle with mental health
        challenges in silence, unable to access the support they need.
      </p>

      <p class="story-text">
        We created a platform where anyone can connect with licensed mental health professionals,
        share their experiences with a supportive community, and find the resources they need to
        thrive—all without judgment or stigma.
      </p>

      <p class="story-text">
        Today, we're proud to serve thousands of Filipinos across the country, providing a safe space
        for mental wellness conversations and professional support.
      </p>
    </div>

    <!-- Right: Stats Card -->
    <div class="story-right">
      <div class="stats-card">
        <div class="stat">
          <div class="stat-number">10,000+</div>
          <div class="stat-label">Active Members</div>
        </div>

        <div class="stat">
          <div class="stat-number">150+</div>
          <div class="stat-label">Licensed Professionals</div>
        </div>

        <div class="stat">
          <div class="stat-number">50,000+</div>
          <div class="stat-label">Support Sessions</div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ===== MISSION & VISION SECTION ===== -->
<section id="mission-vision" class="mv-section">
  <div class="mv-container">

    <!-- Mission Card -->
    <div class="mv-card">
      <div class="mv-icon mv-icon-mission">
        ⚡
      </div>

      <h3 class="mv-title">Our Mission</h3>
      <p class="mv-text">
        To democratize mental health support in the Philippines by providing
        accessible, professional, and compassionate care to everyone who needs
        it, regardless of their background or circumstances.
      </p>
    </div>

    <!-- Vision Card -->
    <div class="mv-card">
      <div class="mv-icon mv-icon-vision">
        👁
      </div>

      <h3 class="mv-title">Our Vision</h3>
      <p class="mv-text">
        A Philippines where mental health is openly discussed, properly
        supported, and never stigmatized. Where every person has the tools and
        resources to live their best, healthiest life.
      </p>
    </div>

  </div>
</section>

<!-- ===== CORE VALUES SECTION ===== -->
<section id="core-values" class="values-section">
  <div class="values-container">
    <h2 class="values-title">Our Core Values</h2>
    <p class="values-subtitle">These principles guide everything we do at AskDocPH</p>

    <div class="values-grid">
      <div class="value-item">
        <div class="value-icon">❤</div>
        <h3 class="value-name">Compassion</h3>
        <p class="value-text">
          We approach every interaction with empathy, understanding, and genuine care for our community members.
        </p>
      </div>

      <div class="value-item">
        <div class="value-icon">🔒</div>
        <h3 class="value-name">Privacy</h3>
        <p class="value-text">
          Your trust is sacred. We protect your privacy with industry-leading security and complete confidentiality.
        </p>
      </div>

      <div class="value-item">
        <div class="value-icon">👥</div>
        <h3 class="value-name">Community</h3>
        <p class="value-text">
          Together we're stronger. We foster a supportive environment where everyone belongs and feels heard.
        </p>
      </div>

      <div class="value-item">
        <div class="value-icon">✅</div>
        <h3 class="value-name">Excellence</h3>
        <p class="value-text">
          We work only with licensed, qualified professionals who meet the highest standards of care.
        </p>
      </div>

      <div class="value-item">
        <div class="value-icon">🏠</div>
        <h3 class="value-name">Accessibility</h3>
        <p class="value-text">
          Mental health support should be available to everyone, everywhere, at any time they need it.
        </p>
      </div>

      <div class="value-item">
        <div class="value-icon">🙂</div>
        <h3 class="value-name">No Judgment</h3>
        <p class="value-text">
          This is a safe space where you can be yourself without fear of stigma or discrimination.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ===== MEET OUR TEAM ===== -->
<section id="team-section" class="team-section">
  <div class="team-container">
    <h2 class="team-title">Meet Our Team</h2>
    <p class="team-subtitle">Passionate professionals dedicated to your mental wellness</p>

    <div class="team-grid">

      <!-- Member 1 -->
      <div class="team-card">
        <div class="team-avatar teal">DR</div>
        <div class="team-body">
          <h3 class="team-name">Dr. Maria Santos</h3>
          <div class="team-role">Chief Psychiatrist</div>
          <p class="team-text">
            15+ years experience in clinical psychiatry and mental health advocacy
          </p>
        </div>
      </div>

      <!-- Member 2 -->
      <div class="team-card">
        <div class="team-avatar orange">JC</div>
        <div class="team-body">
          <h3 class="team-name">Juan Cruz</h3>
          <div class="team-role">CEO &amp; Founder</div>
          <p class="team-text">
            Tech entrepreneur passionate about mental health accessibility
          </p>
        </div>
      </div>

      <!-- Member 3 -->
      <div class="team-card">
        <div class="team-avatar teal">AL</div>
        <div class="team-body">
          <h3 class="team-name">Ana Lopez</h3>
          <div class="team-role">Community Director</div>
          <p class="team-text">
            Building supportive communities and fostering meaningful connections
          </p>
        </div>
      </div>

      <!-- Member 4 -->
      <div class="team-card">
        <div class="team-avatar orange">MR</div>
        <div class="team-body">
          <h3 class="team-name">Dr. Miguel Reyes</h3>
          <div class="team-role">Clinical Psychologist</div>
          <p class="team-text">
            Specializing in anxiety, depression, and trauma-informed care
          </p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ===== FINAL CTA SECTION ===== -->
<section id="cta-section" class="cta-section">
  <div class="cta-container">
    <h2 class="cta-title">Ready to Start Your Journey?</h2>
    <p class="cta-text">
      Join thousands of Filipinos who have found support, understanding,
      and healing through AskDocPH.
    </p>

    <a href="#login-section" class="cta-btn">Get Started Today</a>
  </div>
</section>
</body>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
  <div class="footer-container">
    © {{ date('Y') }} AskDocPH. All rights reserved.

  </div>
</footer>

</html>
