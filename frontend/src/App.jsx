import { BrowserRouter, Link, Route, Routes } from "react-router-dom";
import OpsPage from "./OpsPage";

export default function App() {
  return (
    <BrowserRouter>
      <nav>
        <Link to="/ops">Daily Ops</Link>
      </nav>
      <Routes>
        <Route path="/ops" element={<OpsPage />} />
      </Routes>
    </BrowserRouter>
  );
}
