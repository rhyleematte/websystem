import { useState } from "react";
import { Eye, EyeOff, Mail, Lock, User } from "lucide-react";
import { Button } from "./ui/button";
import { Input } from "./ui/input";
import { Label } from "./ui/label";
import { Checkbox } from "./ui/checkbox";


interface RegistrationPageProps {
  onSwitchToLogin: () => void;
}

export function RegistrationPage({ onSwitchToLogin }: RegistrationPageProps) {
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [formData, setFormData] = useState({
    fullName: "",
    email: "",
    password: "",
    confirmPassword: "",
    agreeToTerms: false,
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (formData.password !== formData.confirmPassword) {
      alert("Passwords don't match!");
      return;
    }
    if (!formData.agreeToTerms) {
      alert("Please agree to the terms and conditions");
      return;
    }
    console.log("Registration submitted:", formData);
  };

  const updateFormData = (field: string, value: string | boolean) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  return (
    <div className="min-h-screen bg-[#F8FAFB] flex items-center justify-center p-4">
      <div className="w-full max-w-5xl">
        <div className="bg-white rounded-2xl shadow-xl overflow-hidden">
          <div className="grid lg:grid-cols-2">
            {/* Left side - Form */}
            <div className="p-8 lg:p-12 order-2 lg:order-1">
              <div className="max-w-md mx-auto">
                {/* Logo */}
                <div className="mb-8">
                  <img
                    src="/AskDocPH.png"
                    alt="AskDocPH Logo"
                    className="h-16 mx-auto mb-8"
                  />
                </div>

                {/* Header */}
                <div className="mb-8">
                  <h1 className="text-3xl font-bold text-[#2C4E6B] mb-2">
                    Get Started
                  </h1>
                  <p className="text-slate-600">
                    Create your account and begin your wellness journey
                  </p>
                </div>

                {/* Divider */}
                <div className="relative my-6">
                  <div className="absolute inset-0 flex items-center">
                    <div className="w-full border-t border-slate-200" />
                  </div>
                  <div className="relative flex justify-center text-sm">
                    <span className="px-4 bg-white text-slate-500">Register with email</span>
                  </div>
                </div>

                {/* Email/Password Form */}
                <form onSubmit={handleSubmit} className="space-y-4">
                  {/* Full Name Field */}
                  <div className="space-y-2">
                    <Label htmlFor="fullName" className="text-slate-700">
                      Full Name
                    </Label>
                    <div className="relative">
                      <User className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                      <Input
                        id="fullName"
                        type="text"
                        placeholder="John Doe"
                        value={formData.fullName}
                        onChange={(e) => updateFormData("fullName", e.target.value)}
                        required
                        className="h-11 pl-10 border-slate-300 focus:border-[#0D9BA6] focus:ring-[#0D9BA6]"
                      />
                    </div>
                  </div>

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
                        value={formData.email}
                        onChange={(e) => updateFormData("email", e.target.value)}
                        required
                        className="h-11 pl-10 border-slate-300 focus:border-[#0D9BA6] focus:ring-[#0D9BA6]"
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
                        placeholder="Create a strong password"
                        value={formData.password}
                        onChange={(e) => updateFormData("password", e.target.value)}
                        required
                        className="h-11 pl-10 pr-10 border-slate-300 focus:border-[#0D9BA6] focus:ring-[#0D9BA6]"
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

                  {/* Confirm Password Field */}
                  <div className="space-y-2">
                    <Label htmlFor="confirmPassword" className="text-slate-700">
                      Confirm Password
                    </Label>
                    <div className="relative">
                      <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                      <Input
                        id="confirmPassword"
                        type={showConfirmPassword ? "text" : "password"}
                        placeholder="Re-enter your password"
                        value={formData.confirmPassword}
                        onChange={(e) => updateFormData("confirmPassword", e.target.value)}
                        required
                        className="h-11 pl-10 pr-10 border-slate-300 focus:border-[#0D9BA6] focus:ring-[#0D9BA6]"
                      />
                      <button
                        type="button"
                        onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                      >
                        {showConfirmPassword ? (
                          <EyeOff className="w-5 h-5" />
                        ) : (
                          <Eye className="w-5 h-5" />
                        )}
                      </button>
                    </div>
                  </div>

                  {/* Terms and Conditions */}
                  <div className="flex items-start space-x-2 pt-2">
                    <Checkbox
                      id="terms"
                      checked={formData.agreeToTerms}
                      onCheckedChange={(checked) => updateFormData("agreeToTerms", checked as boolean)}
                    />
                    <Label
                      htmlFor="terms"
                      className="text-sm text-slate-600 cursor-pointer leading-relaxed"
                    >
                      I agree to the{" "}
                      <a href="#" className="text-[#0D9BA6] hover:text-[#0B8290] underline">
                        Terms of Service
                      </a>{" "}
                      and{" "}
                      <a href="#" className="text-[#0D9BA6] hover:text-[#0B8290] underline">
                        Privacy Policy
                      </a>
                    </Label>
                  </div>

                  {/* Submit Button */}
                  <Button
                    type="submit"
                    className="w-full bg-[#0D9BA6] hover:bg-[#0B8290] text-white h-12 font-medium"
                  >
                    Create Account
                  </Button>
                </form>

                {/* Sign in link */}
                <p className="mt-6 text-center text-sm text-slate-600">
                  Already have an account?{" "}
                  <button
                    onClick={onSwitchToLogin}
                    className="text-[#F5A623] hover:text-[#E09616] font-medium"
                  >
                    Sign in
                  </button>
                </p>
              </div>
            </div>

            {/* Right side - Info */}
            <div className="bg-gradient-to-br from-[#0D9BA6] to-[#0B8290] p-8 lg:p-12 flex flex-col justify-center text-white order-1 lg:order-2">
              <div className="max-w-md">
                <h2 className="text-3xl font-bold mb-6">
                  Start your wellness journey today
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
                      <h3 className="font-semibold mb-2">Expert Support</h3>
                      <p className="text-white/80 text-sm">
                        Get guidance from licensed mental health professionals who care.
                      </p>
                    </div>
                  </div>

                  <div className="flex gap-4">
                    <div className="flex-shrink-0">
                      <div className="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                      </div>
                    </div>
                    <div>
                      <h3 className="font-semibold mb-2">24/7 Community</h3>
                      <p className="text-white/80 text-sm">
                        Connect anytime with a supportive community that understands.
                      </p>
                    </div>
                  </div>

                  <div className="flex gap-4">
                    <div className="flex-shrink-0">
                      <div className="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                      </div>
                    </div>
                    <div>
                      <h3 className="font-semibold mb-2">Privacy First</h3>
                      <p className="text-white/80 text-sm">
                        Your conversations are private, secure, and completely confidential.
                      </p>
                    </div>
                  </div>
                </div>

                <div className="mt-12 p-6 bg-white/10 rounded-lg backdrop-blur-sm">
                  <p className="text-sm italic">
                    "Joining AskDocPH was the best decision I made for my mental health. I finally found a place where I feel understood."
                  </p>
                  <p className="text-sm mt-3 font-medium">— Carlos M., Quezon City</p>
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
  );
}
