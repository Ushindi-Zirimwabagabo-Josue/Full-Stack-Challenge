import React, { useState, useEffect } from 'react';

interface Report {
  id: string;
  title: string;
  state: string;
  payload: any;
}

const App: React.FC = () => {
  const [reports, setReports] = useState<Report[]>([]);
  const [alert, setAlert] = useState<{ message: string, type: 'success' | 'error' } | null>(null);

  useEffect(() => {
    fetchReports();
  }, []);

  const fetchReports = async () => {
    try {
      const response = await fetch('http://localhost:8000/reports');
      const data = await response.json();
      setReports(data);
    } catch (error) {
      console.error('Error fetching reports:', error);
    }
  };

  const showAlert = (message: string, type: 'success' | 'error') => {
    setAlert({ message, type });
    setTimeout(() => {
      setAlert(null);
    }, 3000);
  };

  const blockReport = async (reportId: string) => {
    try {
      await fetch(`http://localhost:8000/reports/block/${reportId}`, {
        method: 'DELETE',
      });

      const updatedReports = reports.filter(report => report.id !== reportId);
      setReports(updatedReports);

      showAlert('Report blocked successfully', 'success');
    } catch (error) {
      console.error('Error blocking report:', error);
      showAlert('Failed to block report', 'error');
    }
  };

  const resolveReport = async (reportId: string) => {
    try {
      // Update the state locally before making the API call (just for testing purpose, to reflect the change)
      const updatedReports = reports.map(report =>
        report.id === reportId ? { ...report, state: 'CLOSED' } : report
      );
      setReports(updatedReports);

      const response = await fetch(`http://localhost:8000/reports/${reportId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ state: 'CLOSED' }),
      });

      showAlert('Report resolved successfully', 'success');

      if (!response.ok) {
        console.error('Failed to resolve report. Server returned:', response.statusText);
      }
    } catch (error) {
      console.error('Error resolving report:', error);
      showAlert('Failed to resolve report', 'error');
    }
  };


  const getLastPartOfId = (id: string) => {
    const idParts = id.split('-');
    return idParts[idParts.length - 1];
  };

  return (
    <div className='w-fit mx-auto my-4'>
      <h1 className='mb-2 text-xl font-bold'>Reports</h1>

      {alert && (
        <div>
          {alert.message}
        </div>
      )}

      <div className='border border-b-0 pt-2 rounded'>
        {reports.map((report) => (
          <div key={report.id} className="flex gap-[4rem] p-3 border-b">
            <div className=''>
              <p>ID: {getLastPartOfId(report.id)}</p>
              <p>State: {report.state}</p>
              <button className='underline text-blue-700'>Details</button>
            </div>
            <div className=''>
              <div>
                <span className=''>Type: </span>
                <span className='capitalize'>{report.payload.reportType}</span>
              </div>
              <div>
                <span className=''>Message: </span>
                <span className=''>Some message...</span>
              </div>
            </div>
            <div className=' flex flex-col gap-2'>
              <button className='border rounded-lg px-5 py-1' onClick={() => blockReport(report.id)}>Block</button>
              <button className='border rounded-lg px-5 py-1' onClick={() => resolveReport(report.id)}>Resolve</button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default App;
