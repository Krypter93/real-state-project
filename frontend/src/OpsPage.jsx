import { useState } from "react";

const canManage = ["finance", "manager"].includes(localStorage.getItem("role") || "");

export default function OpsPage() {
  const [running, setRunning] = useState(false);
  const [closeLoading, setCloseLoading] = useState(false);
  const [weekStart, setWeekStart] = useState("");
  const [results, setResults] = useState([]);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [weekClosed, setWeekClosed] = useState(false);

  const role = localStorage.getItem("role") || "";

  const runDailySync = async () => {
    setRunning(true);
    setError("");
    setSuccess("");
    try {
      const res = await fetch("/ops/daily-run", {
        method: "POST",
        headers: { "X-Role": role },
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.detail || "Daily sync failed");
      setResults(data.sources || []);
      setSuccess("Daily sync finished");
    } catch (e) {
      setError(e.message);
    } finally {
      setRunning(false);
    }
  };

  const closeWeek = async () => {
    if (!weekStart) return;
    if (!window.confirm(`Close week ${weekStart}?`)) return;
    setCloseLoading(true);
    setError("");
    setSuccess("");
    try {
      const res = await fetch(`/weeks/${weekStart}/close`, {
        method: "POST",
        headers: { "X-Role": role },
      });
      const data = await res.json();
      if (res.status === 409) {
        setWeekClosed(true);
        throw new Error(data.detail);
      }
      if (!res.ok) throw new Error(data.detail || "Close week failed");
      setWeekClosed(true);
      setSuccess("Week closed successfully");
    } catch (e) {
      setError(e.message);
    } finally {
      setCloseLoading(false);
    }
  };

  if (!canManage) return <p>Only finance/manager can access Daily Ops.</p>;

  return (
    <section>
      <h2>Daily Ops</h2>

      <button onClick={runDailySync} disabled={running}>
        {running ? "Running..." : "Run Daily Sync"}
      </button>

      <div style={{ marginTop: 16 }}>
        <input
          type="date"
          value={weekStart}
          onChange={(e) => setWeekStart(e.target.value)}
        />
        <button onClick={closeWeek} disabled={closeLoading || weekClosed || !weekStart}>
          {closeLoading ? "Closing..." : "Close week"}
        </button>
        {weekClosed && <small> Week already closed.</small>}
      </div>

      <div style={{ marginTop: 16 }}>
        <a href={weekStart ? `/weeks/${weekStart}/export` : "#"}>Export CSV</a>
      </div>

      {error && <p style={{ color: "red" }}>{error}</p>}
      {success && <p style={{ color: "green" }}>{success}</p>}

      <table style={{ marginTop: 16 }}>
        <thead>
          <tr>
            <th>Source</th>
            <th>Processed</th>
            <th>Inserted</th>
            <th>Duplicates</th>
            <th>Skipped</th>
            <th>Last row</th>
            <th>Error</th>
          </tr>
        </thead>
        <tbody>
          {results.map((r) => (
            <tr key={r.source}>
              <td>{r.source}</td>
              <td>{r.processed_new_rows}</td>
              <td>{r.inserted_sales}</td>
              <td>{r.duplicates_marked}</td>
              <td>{r.skipped_rows}</td>
              <td>{r.last_row_synced}</td>
              <td>{r.error || "-"}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </section>
  );
}
