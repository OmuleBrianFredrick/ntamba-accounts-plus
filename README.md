# Ntamba Accounts Plus

An enterprise-grade accounting platform for **Ntamba Products**, built around an offline-first field sales capture and reconciliation workflow.

This system replaces unstructured WhatsApp reporting with a robust two-way platform: field agents use an installable Progressive Web App (PWA) to capture sales, debt clearances, and product exchanges in real-time (even without internet), while accountants use a centralized web dashboard to review, reconcile, and manage the books — including the financial ledger, credit/debt management, and inventory movements.

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

> Authoritative source: *Ntamba Sales and Accounts System Plan.md* (decision 9 & Technical design section). Stack pending final confirmation — see open questions.

| Layer | Technology | Purpose |
| :--- | :--- | :--- |
| **Backend framework** | Laravel (latest stable) | API + business rules, role permissions, audit log, idempotent sync endpoints. |
| **Database** | PostgreSQL (or MySQL) | Relational store; company-scoped tables; JSON settings columns. |
| **Frontend** | Vue 3 + Vite + PWA plugin | One code base, two faces: phone layout (agent app) and desktop layout (accountant console). |
| **Offline storage (phone)** | IndexedDB + service worker | Local price list, client search, own stock/sales; write-ahead sync queue with unique IDs. |
| **Realtime console** | Laravel Reverb (WebSocket) | Live board updates, 20-second polling fallback. |
| **Hosting** | Single VPS (2 vCPU / 4 GB) | Daily DB backups (30 days), weekly off-server copy, staging copy for training. |

## 🏗 System Architecture

```text
[ Agent Phone (Vue 3 PWA) ] --(IndexedDB queue, idempotent sync by phone-made UUID)--> [ Laravel API ]
              |                                                                                |
   (service worker + Add to Home Screen)                                            [ PostgreSQL (company-scoped) ]
              |                                                                                |
[ Accountant Console (desktop layout) ] <--(Laravel Reverb WebSocket / 20s polling)-----------+
                          (audit log, alerts, stock movements, Phase-2 ledger postings)
```

## 🗺 Roadmap

The build follows the plan document's phases, each ending in a sign-off gate:

- **Phase 0 – Preparation:** answer open questions, phone mock-ups of sale flow / stock confirm / day close, Phase 1 DB design, test-case checklist from the workbook, hosting/domain decisions.
- **Phase 1 – Sales & stock (M1–M9):** foundation (auth, roles, companies, products, prices, routes, audit log) → stock engine (trips, issue/confirm/lock, movements) → clients (search, duplicate check, merge) → sales capture → offline & sync → evidence, payments, debtors → day close & handover → console, targets, alerts, reports → pilot (Gloria/Kampala, 10 parallel days) then go-live.
- **Phase 2 – Finance & ledger:** chart of accounts + posting engine, expenses/income, service providers, cash book, costing (recipes/batches/unit cost), financial reports.
- **Phase 3 – Imports & messaging:** import wizard, Zoho Books/Inventory/CRM + piclesale.com connections, SMS/WhatsApp reminders, statement matching, second workspace.

Gate for Phase 1: ten working days of parallel running where the system matches the workbook to the shilling.

## 🤝 Contributing

All development for the Ntamba Accounts Plus is pushed to this repository. Keep commits focused, and update this README as the architecture evolves.
