import { useState } from "react";
import { LoginPage } from "./components/LoginPage";
import { RegistrationPage } from "./components/RegistrationPage";

export default function App() {
  const [showRegistration, setShowRegistration] = useState(false);

  return (
    <div className="size-full">
      {showRegistration ? (
        <RegistrationPage onSwitchToLogin={() => setShowRegistration(false)} />
      ) : (
        <LoginPage onSwitchToRegister={() => setShowRegistration(true)} />
      )}
    </div>
  );
}