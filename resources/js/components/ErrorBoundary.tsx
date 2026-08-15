import React, { Component, ErrorInfo, ReactNode } from "react";
import { db } from "../lib/db";

interface Props {
  children?: ReactNode;
}

interface State {
  hasError: boolean;
  error: Error | null;
}

export class ErrorBoundary extends Component<Props, State> {
  public state: State = {
    hasError: false,
    error: null
  };

  public static getDerivedStateFromError(error: Error): State {
    return { hasError: true, error };
  }

  public componentDidCatch(error: Error, errorInfo: ErrorInfo) {
    console.error("Uncaught error:", error, errorInfo);
  }

  private handleReset = async () => {
    // Clear all possible corrupted states
    localStorage.clear();
    
    try {
        // Clear IndexedDB
        await db.delete();
    } catch (e) {
        console.error("Failed to delete db", e);
    }
    
    // Unregister service workers
    if ("serviceWorker" in navigator) {
        try {
            const registrations = await navigator.serviceWorker.getRegistrations();
            for (let registration of registrations) {
                await registration.unregister();
            }
        } catch (e) {
            console.error("Failed to unregister SW", e);
        }
    }
    
    // Hard reload from server
    window.location.href = "/pos";
  };

  public render() {
    if (this.state.hasError) {
      return (
        <div className="min-h-screen flex items-center justify-center bg-slate-50 p-4">
          <div className="bg-white p-8 rounded-3xl shadow-xl max-w-md w-full text-center">
            <div className="w-20 h-20 bg-rose-100 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg xmlns="http://www.w3.org/2000/svg" className="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <h1 className="text-2xl font-black text-slate-800 mb-2">Oops, Aplikasi Terhenti!</h1>
            <p className="text-slate-500 mb-8">
              Terjadi kesalahan pada sistem atau data memori lokal Anda tidak sinkron. Silakan lakukan reset aplikasi untuk memperbaikinya.
            </p>
            <button
              onClick={this.handleReset}
              className="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-lg shadow-indigo-200"
            >
              Reset & Muat Ulang Aplikasi
            </button>
            <p className="text-xs text-slate-400 mt-4 font-mono break-words bg-slate-50 p-2 rounded-lg">
              {this.state.error?.message}
            </p>
          </div>
        </div>
      );
    }

    return this.props.children;
  }
}
