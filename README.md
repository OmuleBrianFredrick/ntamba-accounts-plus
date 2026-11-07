# Ntamba Sales Management System

An enterprise-grade, offline-first field sales capture and reconciliation system designed specifically for **Ntamba Products**. 

This system replaces unstructured WhatsApp reporting with a robust two-way platform: field agents use an installable Progressive Web App (PWA) to capture sales, debt clearances, and product exchanges in real-time (even without internet), while accountants use a centralized web dashboard to review, reconcile, and manage credit and inventory.

## 🌟 Core Capabilities

### 📱 Agent Mobile Interface (PWA)
* **Offline-First Architecture:** Agents can capture visits in remote areas with zero connectivity. Data queues locally and auto-syncs when the network returns.
* **Comprehensive Transaction Capture:** Supports Cash, Credit, Partial Payments, Mobile Merchant, and Debt Clearance.
* **Exchange & Returns Tracking:** Logs returned items (e.g., expiry, leakage, foam) and replacement items issued, with mandatory reason codes.
* **Client Debt Visibility:** Warns agents in real-time if a selected shop has outstanding or overdue credit before a new order is placed.
* **Installable App:** Functions as a native-like Android app via PWA manifest and service workers.

### 💼 Accountant Dashboard (Web CMS)
* **Two-Way Reconciliation Queue:** Accountants can review, approve, edit, or reject agent submissions. Every action is immutable and logged in the audit trail.
* **Financial Ledger:** Real-time aggregation of Cash Sales, Credit Sales, Debt Recovered, and Merchant payments with 1-click CSV/Excel export.
* **Credit & Debt Management:** Automated credit ledger generation upon visit approval. Includes aging analysis (Current, 1-7, 8-30, 31-60, 60+ days) and manual payment recording.
* **Inventory Movements:** Automatically generates inventory outflows for sales, inflows for returns, and outflows for replacements. Supports opening stock and manual adjustments.

## 🛠 Technology Stack

| Layer | Technology | Purpose |
| :--- | :--- | :--- |
| **Frontend Framework** | Next.js 14 (App Router) | React framework with Server Components and API routes. |
| **Language** | TypeScript | Strict type safety across the entire stack. |
| **Styling** | Tailwind CSS | Utility-first CSS for rapid, responsive UI development. |
| **Backend / Database** | Supabase (PostgreSQL) | Managed Postgres with Row Level Security (RLS) and Auth. |
| **State Management** | Zustand | Lightweight state management with `persist` middleware for offline queues. |
| **Offline / PWA** | `@ducanh2912/next-pwa` | Service worker generation for offline caching and installability. |
| **Icons** | Lucide React | Clean, consistent SVG iconography. |

## 🏗 System Architecture

```text
[ Agent Phone (PWA) ] <--(Offline Queue)--> [ Next.js API (/api/sync) ]
                                                    |
                                                    v
[ Accountant PC (Web) ] <----(Realtime)----> [ Supabase PostgreSQL ]
                                                    |
                                      (Triggers: Credit Ledger, 
                                       Inventory Movements, Audit Log)
