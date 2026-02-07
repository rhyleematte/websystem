import { useState } from "react";
import { Eye, EyeOff, Mail, Lock } from "lucide-react";
import { Button } from "./ui/button";
import { Input } from "./ui/input";
import { Label } from "./ui/label";
import { Checkbox } from "./ui/checkbox";
import { AboutSection } from "./AboutSection";

interface LoginPageProps {
  onSwitchToRegister: () => void;
}

export function LoginPage({ onSwitchToRegister }: LoginPageProps) {
  const [showPassword, setShowPassword] = useState(false);
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [rememberMe, setRememberMe] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    console.log("Login submitted:", { email, password, rememberMe });
  };

  const scrollToAbout = () => {
    const aboutSection = document.getElementById('about-section');
    aboutSection?.scrollIntoView({ behavior: 'smooth' });
  };

  return (
    <div>
      {/* Header */}
      <header className="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-16 pt-2">
            {/* Logo */}
            <div className="flex items-center">
              <img
                src="/AskDocPH.png"
                alt="AskDocPH Logo"
                className="h-12"
              />
            </div>

            {/* Navigation */}
            <nav className="flex items-center gap-8">
              <button
                onClick={scrollToAbout}
                className="text-slate-600 hover:text-[#0D9BA6] font-medium transition-colors"
              >
                About
              </button>

              <button
                onClick={onSwitchToRegister}
                className="bg-[#0D9BA6] hover:bg-[#0B8290] text-white px-6 py-2 rounded-lg font-medium transition-colors"
              >
                Sign Up
              </button>
            </nav>
          </div>
        </div>
      </header>


      {/* Body - Login Form */}
      <div className="min-h-screen bg-[#F8FAFB] flex items-center justify-center p-4">
        <div className="w-full max-w-5xl">
          <div className="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div className="grid lg:grid-cols-2">
              {/* Left side - Form */}
              <div className="p-8 lg:p-12">
                <div className="max-w-md mx-auto">


                  {/* Header */}
                  <div className="mb-8">
                    <h1 className="text-3xl font-bold text-[#2C4E6B] mb-2">
                      Welcome Back
                    </h1>
                    <p className="text-slate-600">
                      Continue your mental wellness journey
                    </p>
                  </div>


                  {/* Divider */}
                  <div className="relative my-6">
                    <div className="absolute inset-0 flex items-center">
                      <div className="w-full border-t border-slate-200" />
                    </div>
                    <div className="relative flex justify-center text-sm">
                      <span className="px-4 bg-white text-slate-500">Sign in with email</span>
                    </div>
                  </div>

                  {/* Email/Password Form */}
                  <form onSubmit={handleSubmit} className="space-y-5">
                    {/* Email Field */}
                    <div className="space-y-2">
                      <Label htmlFor="email" className="text-slate-700">
                        Email Address
                      </Label>
                      <div className="relative">
                        <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                        <Input
                          id="email"
                          type="email"
                          placeholder="you@example.com"
                          value={email}
                          onChange={(e) => setEmail(e.target.value)}
                          required
                          className="h-12 pl-10 border-slate-300 focus:border-[#0D9BA6] focus:ring-[#0D9BA6]"
                        />
                      </div>
                    </div>

                    {/* Password Field */}
                    <div className="space-y-2">
                      <Label htmlFor="password" className="text-slate-700">
                        Password
                      </Label>
                      <div className="relative">
                        <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                        <Input
                          id="password"
                          type={showPassword ? "text" : "password"}
                          placeholder="Enter your password"
                          value={password}
                          onChange={(e) => setPassword(e.target.value)}
                          required
                          className="h-12 pl-10 pr-10 border-slate-300 focus:border-[#0D9BA6] focus:ring-[#0D9BA6]"
                        />
                        <button
                          type="button"
                          onClick={() => setShowPassword(!showPassword)}
                          className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                        >
                          {showPassword ? (
                            <EyeOff className="w-5 h-5" />
                          ) : (
                            <Eye className="w-5 h-5" />
                          )}
                        </button>
                      </div>
                    </div>

                    {/* Remember Me and Forgot Password */}
                    <div className="flex items-center justify-between">
                      <div className="flex items-center space-x-2">
                        <Checkbox
                          id="remember"
                          checked={rememberMe}
                          onCheckedChange={(checked) => setRememberMe(checked as boolean)}
                        />
                        <Label
                          htmlFor="remember"
                          className="text-sm text-slate-600 cursor-pointer"
                        >
                          Remember me
                        </Label>
                      </div>
                      <a href="#" className="text-sm text-[#0D9BA6] hover:text-[#0B8290] font-medium">
                        Forgot password?
                      </a>
                    </div>

                    {/* Submit Button */}
                    <Button
                      type="submit"
                      className="w-full bg-[#0D9BA6] hover:bg-[#0B8290] text-white h-12 font-medium"
                    >
                      Sign In
                    </Button>
                  </form>

                  {/* Sign up link */}
                  <p className="mt-6 text-center text-sm text-slate-600">
                    New to AskDocPH?{" "}
                    <button
                      onClick={onSwitchToRegister}
                      className="text-[#F5A623] hover:text-[#E09616] font-medium"
                    >
                      Create an account
                    </button>
                  </p>
                </div>
              </div>

              {/* Right side - Info */}
              <div className="bg-gradient-to-br from-[#0D9BA6] to-[#0B8290] p-8 lg:p-12 flex flex-col justify-center text-white">
                <div className="max-w-md">
                  <h2 className="text-3xl font-bold mb-6">
                    Your mental health journey starts here
                  </h2>

                  <div className="space-y-6">
                    <div className="flex gap-4">
                      <div className="flex-shrink-0">
                        <div className="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center">
                          <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                          </svg>
                        </div>
                      </div>
                      <div>
                        <h3 className="font-semibold mb-2">Connect with Professionals</h3>
                        <p className="text-white/80 text-sm">
                          Access licensed psychiatrists and mental health experts whenever you need support.
                        </p>
                      </div>
                    </div>

                    <div className="flex gap-4">
                      <div className="flex-shrink-0">
                        <div className="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center">
                          <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                          </svg>
                        </div>
                      </div>
                      <div>
                        <h3 className="font-semibold mb-2">Join Our Community</h3>
                        <p className="text-white/80 text-sm">
                          Share experiences and find support from others who understand what you're going through.
                        </p>
                      </div>
                    </div>

                    <div className="flex gap-4">
                      <div className="flex-shrink-0">
                        <div className="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center">
                          <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                          </svg>
                        </div>
                      </div>
                      <div>
                        <h3 className="font-semibold mb-2">Safe & Confidential</h3>
                        <p className="text-white/80 text-sm">
                          Your privacy matters. All conversations are secure and judgment-free.
                        </p>
                      </div>
                    </div>
                  </div>

                  <div className="mt-12 p-6 bg-white/10 rounded-lg backdrop-blur-sm">
                    <p className="text-sm italic">
                      "AskDocPH helped me find the support I needed during my toughest moments. The community is incredible."
                    </p>
                    <p className="text-sm mt-3 font-medium">— Maria S., Manila</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Footer */}
          <p className="text-center text-slate-500 text-sm mt-8">
            © 2026 AskDocPH. All rights reserved. • <a href="#" className="hover:text-slate-700">Privacy Policy</a> • <a href="#" className="hover:text-slate-700">Terms of Service</a>
          </p>
        </div>
      </div>

      {/* About Us Section */}
      <AboutSection />
    </div>
  );
}