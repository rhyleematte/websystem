
export function AboutSection() {
  return (
    <div className="bg-white" id="about-section">
      {/* Hero Section */}
      <div className="bg-gradient-to-br from-[#0D9BA6] to-[#0B8290] py-20">
        <div className="max-w-6xl mx-auto px-4">
          <div className="text-center text-white">
            <h1 className="text-5xl font-bold mb-6">About AskDocPH</h1>
            <p className="text-xl text-white/90 max-w-3xl mx-auto">
              We're on a mission to make mental health support accessible, affordable, and stigma-free for every Filipino.
            </p>
          </div>
        </div>
      </div>

      {/* Our Story */}
      <div className="max-w-6xl mx-auto px-4 py-20">
        <div className="grid lg:grid-cols-2 gap-12 items-center">
          <div>
            <img
              src="/AskDocPH.png"
              alt="AskDocPH Logo"
              className="h-16 mx-auto mb-8"
            />
            <h2 className="text-4xl font-bold text-[#2C4E6B] mb-6">Our Story</h2>
            <div className="space-y-4 text-slate-600 text-lg leading-relaxed">
              <p>
                AskDocPH was born from a simple observation: too many Filipinos struggle with mental health challenges in silence, unable to access the support they need.
              </p>
              <p>
                We created a platform where anyone can connect with licensed mental health professionals, share their experiences with a supportive community, and find the resources they need to thrive—all without judgment or stigma.
              </p>
              <p>
                Today, we're proud to serve thousands of Filipinos across the country, providing a safe space for mental wellness conversations and professional support.
              </p>
            </div>
          </div>
          <div className="bg-slate-100 rounded-2xl p-12 text-center">
            <div className="space-y-8">
              <div>
                <div className="text-5xl font-bold text-[#0D9BA6] mb-2">10,000+</div>
                <div className="text-slate-600">Active Members</div>
              </div>
              <div>
                <div className="text-5xl font-bold text-[#0D9BA6] mb-2">150+</div>
                <div className="text-slate-600">Licensed Professionals</div>
              </div>
              <div>
                <div className="text-5xl font-bold text-[#0D9BA6] mb-2">50,000+</div>
                <div className="text-slate-600">Support Sessions</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Mission & Vision */}
      <div className="bg-slate-50 py-20">
        <div className="max-w-6xl mx-auto px-4">
          <div className="grid lg:grid-cols-2 gap-12">
            <div className="bg-white p-10 rounded-2xl shadow-sm">
              <div className="w-16 h-16 bg-[#0D9BA6]/10 rounded-xl flex items-center justify-center mb-6">
                <svg className="w-8 h-8 text-[#0D9BA6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
              <h3 className="text-3xl font-bold text-[#2C4E6B] mb-4">Our Mission</h3>
              <p className="text-slate-600 text-lg leading-relaxed">
                To democratize mental health support in the Philippines by providing accessible, professional, and compassionate care to everyone who needs it, regardless of their background or circumstances.
              </p>
            </div>
            <div className="bg-white p-10 rounded-2xl shadow-sm">
              <div className="w-16 h-16 bg-[#F5A623]/10 rounded-xl flex items-center justify-center mb-6">
                <svg className="w-8 h-8 text-[#F5A623]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </div>
              <h3 className="text-3xl font-bold text-[#2C4E6B] mb-4">Our Vision</h3>
              <p className="text-slate-600 text-lg leading-relaxed">
                A Philippines where mental health is openly discussed, properly supported, and never stigmatized. Where every person has the tools and resources to live their best, healthiest life.
              </p>
            </div>
          </div>
        </div>
      </div>

      {/* Our Values */}
      <div className="max-w-6xl mx-auto px-4 py-20">
        <div className="text-center mb-16">
          <h2 className="text-4xl font-bold text-[#2C4E6B] mb-4">Our Core Values</h2>
          <p className="text-slate-600 text-lg max-w-2xl mx-auto">
            These principles guide everything we do at AskDocPH
          </p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div className="text-center">
            <div className="w-20 h-20 bg-[#0D9BA6]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg className="w-10 h-10 text-[#0D9BA6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            </div>
            <h4 className="text-xl font-bold text-[#2C4E6B] mb-3">Compassion</h4>
            <p className="text-slate-600">
              We approach every interaction with empathy, understanding, and genuine care for our community members.
            </p>
          </div>

          <div className="text-center">
            <div className="w-20 h-20 bg-[#0D9BA6]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg className="w-10 h-10 text-[#0D9BA6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </div>
            <h4 className="text-xl font-bold text-[#2C4E6B] mb-3">Privacy</h4>
            <p className="text-slate-600">
              Your trust is sacred. We protect your privacy with industry-leading security and complete confidentiality.
            </p>
          </div>

          <div className="text-center">
            <div className="w-20 h-20 bg-[#0D9BA6]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg className="w-10 h-10 text-[#0D9BA6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <h4 className="text-xl font-bold text-[#2C4E6B] mb-3">Community</h4>
            <p className="text-slate-600">
              Together we're stronger. We foster a supportive environment where everyone belongs and feels heard.
            </p>
          </div>

          <div className="text-center">
            <div className="w-20 h-20 bg-[#0D9BA6]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg className="w-10 h-10 text-[#0D9BA6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h4 className="text-xl font-bold text-[#2C4E6B] mb-3">Excellence</h4>
            <p className="text-slate-600">
              We work only with licensed, qualified professionals who meet the highest standards of care.
            </p>
          </div>

          <div className="text-center">
            <div className="w-20 h-20 bg-[#0D9BA6]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg className="w-10 h-10 text-[#0D9BA6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
            </div>
            <h4 className="text-xl font-bold text-[#2C4E6B] mb-3">Accessibility</h4>
            <p className="text-slate-600">
              Mental health support should be available to everyone, everywhere, at any time they need it.
            </p>
          </div>

          <div className="text-center">
            <div className="w-20 h-20 bg-[#0D9BA6]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <svg className="w-10 h-10 text-[#0D9BA6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h4 className="text-xl font-bold text-[#2C4E6B] mb-3">No Judgment</h4>
            <p className="text-slate-600">
              This is a safe space where you can be yourself without fear of stigma or discrimination.
            </p>
          </div>
        </div>
      </div>

      {/* Team Section */}
      <div className="bg-slate-50 py-20">
        <div className="max-w-6xl mx-auto px-4">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-[#2C4E6B] mb-4">Meet Our Team</h2>
            <p className="text-slate-600 text-lg max-w-2xl mx-auto">
              Passionate professionals dedicated to your mental wellness
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow">
              <div className="aspect-square bg-gradient-to-br from-[#0D9BA6] to-[#0B8290] flex items-center justify-center">
                <div className="text-white text-6xl font-bold">DR</div>
              </div>
              <div className="p-6 text-center">
                <h4 className="text-xl font-bold text-[#2C4E6B] mb-1">Dr. Maria Santos</h4>
                <p className="text-[#0D9BA6] text-sm mb-3">Chief Psychiatrist</p>
                <p className="text-slate-600 text-sm">
                  15+ years experience in clinical psychiatry and mental health advocacy
                </p>
              </div>
            </div>

            <div className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow">
              <div className="aspect-square bg-gradient-to-br from-[#F5A623] to-[#E09616] flex items-center justify-center">
                <div className="text-white text-6xl font-bold">JC</div>
              </div>
              <div className="p-6 text-center">
                <h4 className="text-xl font-bold text-[#2C4E6B] mb-1">Juan Cruz</h4>
                <p className="text-[#0D9BA6] text-sm mb-3">CEO & Founder</p>
                <p className="text-slate-600 text-sm">
                  Tech entrepreneur passionate about mental health accessibility
                </p>
              </div>
            </div>

            <div className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow">
              <div className="aspect-square bg-gradient-to-br from-[#0D9BA6] to-[#0B8290] flex items-center justify-center">
                <div className="text-white text-6xl font-bold">AL</div>
              </div>
              <div className="p-6 text-center">
                <h4 className="text-xl font-bold text-[#2C4E6B] mb-1">Ana Lopez</h4>
                <p className="text-[#0D9BA6] text-sm mb-3">Community Director</p>
                <p className="text-slate-600 text-sm">
                  Building supportive communities and fostering meaningful connections
                </p>
              </div>
            </div>

            <div className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow">
              <div className="aspect-square bg-gradient-to-br from-[#F5A623] to-[#E09616] flex items-center justify-center">
                <div className="text-white text-6xl font-bold">MR</div>
              </div>
              <div className="p-6 text-center">
                <h4 className="text-xl font-bold text-[#2C4E6B] mb-1">Dr. Miguel Reyes</h4>
                <p className="text-[#0D9BA6] text-sm mb-3">Clinical Psychologist</p>
                <p className="text-slate-600 text-sm">
                  Specializing in anxiety, depression, and trauma-informed care
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* CTA Section */}
      <div className="bg-gradient-to-br from-[#0D9BA6] to-[#0B8290] py-20">
        <div className="max-w-4xl mx-auto px-4 text-center text-white">
          <h2 className="text-4xl font-bold mb-6">Ready to Start Your Journey?</h2>
          <p className="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
            Join thousands of Filipinos who have found support, understanding, and healing through AskDocPH.
          </p>
          <a
            href="#top"
            className="inline-block bg-white text-[#0D9BA6] px-8 py-4 rounded-lg font-semibold hover:bg-slate-100 transition-colors"
          >
            Get Started Today
          </a>
        </div>
      </div>

      {/* Footer */}
      <footer className="bg-[#2C4E6B] text-white py-12">
        <div className="max-w-6xl mx-auto px-4">
          <div className="grid md:grid-cols-4 gap-8 mb-8">
            <div>
              <p className="text-white/80 text-sm">
                Making mental health support accessible to every Filipino.
              </p>
            </div>
            <div>
              <h4 className="font-semibold mb-4">Company</h4>
              <ul className="space-y-2 text-sm text-white/80">
                <li><a href="#about-section" className="hover:text-white">About Us</a></li>
                <li><a href="#" className="hover:text-white">Careers</a></li>
                <li><a href="#" className="hover:text-white">Press</a></li>
                <li><a href="#" className="hover:text-white">Blog</a></li>
              </ul>
            </div>
            <div>
              <h4 className="font-semibold mb-4">Support</h4>
              <ul className="space-y-2 text-sm text-white/80">
                <li><a href="#" className="hover:text-white">Help Center</a></li>
                <li><a href="#" className="hover:text-white">Contact Us</a></li>
                <li><a href="#" className="hover:text-white">Crisis Resources</a></li>
                <li><a href="#" className="hover:text-white">FAQs</a></li>
              </ul>
            </div>
            <div>
              <h4 className="font-semibold mb-4">Legal</h4>
              <ul className="space-y-2 text-sm text-white/80">
                <li><a href="#" className="hover:text-white">Privacy Policy</a></li>
                <li><a href="#" className="hover:text-white">Terms of Service</a></li>
                <li><a href="#" className="hover:text-white">Cookie Policy</a></li>
                <li><a href="#" className="hover:text-white">Disclaimer</a></li>
              </ul>
            </div>
          </div>
          <div className="border-t border-white/20 pt-8">
            <div className="flex flex-col md:flex-row justify-between items-center gap-4">
              <p className="text-white/60 text-sm">
                © 2026 AskDocPH. All rights reserved.
              </p>
              <div className="flex gap-6">
                <a href="#" className="text-white/60 hover:text-white transition-colors">
                  <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                  </svg>
                </a>
                <a href="#" className="text-white/60 hover:text-white transition-colors">
                  <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                  </svg>
                </a>
                <a href="#" className="text-white/60 hover:text-white transition-colors">
                  <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z" />
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}