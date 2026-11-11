# Ntamba Sales and Accounts System Plan

Nov 10, 2026 · @Mimskak Angel

Ntamba's agents record sales and stock on their own phones while the accountant sees and controls everything live. We build sales and stock first (Phase 1), then finance, the double-entry ledger and cost of production (Phase 2), then imports and extra alerts (Phase 3).

## At a glance

&#91;embedded content: system overview · phones, server, office, later phases\]

Agents record sales on their phones, even offline, and each phone sends its records to one server when signal returns. The accountant and the production manager work from a computer on the same records: they see every agent live, issue and count stock, verify evidence and confirm cash. The dashed parts arrive in Phases 2 and 3 and read from the same records, so nothing is typed twice.

## Decisions confirmed

Your answers set the build order, the lock rules and the data we store. Each is listed with what it changes.

| # | Topic | Your answer | What it means for the build |
| --- | --- | --- | --- |
| 1 | Ledger | Both, sales and stock first | Phase 1 builds sales, stock, clients, debtors and cash handover. Phase 2 adds the double-entry ledger and posts the Phase 1 history into it, so nothing is typed twice. |
| 2 | Corrections | Agents may correct their own entries until the day closes | Agents edit their own sales until they close the day. After that only the accountant edits, and every change is logged with the old value, the new value and a reason. |
| 3 | Phones | Agents' own phones | An installable web app, no app store. Works on Android and iPhone, PIN sign-in, small photos, and a remote sign-out if a phone is lost. |
| 4 | Opening stock | Issued at the start of each trip; the accountant and the production manager both manage it | Either role issues or adjusts trip stock. The agent can only confirm receipt. After confirmation it is locked; later changes are dated top-ups or adjustments with a reason. |
| 5 | Cash handover | Daily | A handover step closes every working day, even in the middle of a multi-day trip. |
| 6 | Cost prices | You hold the full details; cost of production will be calculated | Phase 1 stores a unit cost per product, hidden from agents. Phase 2 adds recipes, batches, unit cost and gross profit. |
| 7 | Targets | Per route, updated daily | One default target for every route: UGX 100,000 a day, shown to agents as motivation and never enforced. Units per product stay optional. Dashboards compare the day's progress with it. |
| 8 | Other workspaces | Ntamba now, customisable for others | Every record carries a company id. Products, routes, payment methods, expense types and labels live in settings, not in code. A multi-company admin screen is not part of version 1. |
| 9 | Technology | You agreed with my recommendation | Laravel with MySQL or PostgreSQL, an installable offline-capable web app, role permissions, an audit log, a VPS with daily backups, idempotent sync, and `external_id` fields for later imports. |
| 10 | Evidence | The agent must submit or ask the client for evidence of each cash transaction | Every payment carries evidence: a mobile money reference and screenshot, a bank slip, or a signed note for physical cash. The accountant verifies it. |
| 11 | Plan format | A detailed, step-by-step listing | This document lists every step in order, by role. |

I read decision 10 as covering every payment received, whether mobile money, bank or physical cash. If you meant physical cash only, tell me and I will narrow it.

## Who uses the system

Four roles, each seeing only what its job needs. An agent never sees another agent's sales, costs or profit.

**One exception, approved on 11 Nov.** When an agent searches for a client, the app shows an Overdue balance marker if another agent's sale to that client is overdue. It shows no amount.

| Capability | Agent | Accountant | Production manager | Admin |
| --- | --- | --- | --- | --- |
| Record sales and payments | Own only | Any agent, tagged "entered by accountant for the agent" | No | No |
| Edit a sale | Own, until the day is closed | Any, any time, with a reason | No | Any, with a reason |
| See other agents' data | No | All | Stock and items sold only | All |
| Issue, top up or adjust trip stock | No, confirms receipt only | Yes | Yes | Yes |
| Count stock at trip end | No | Yes | Yes | Yes |
| Confirm the daily cash handover | No, declares it | Yes | No | Yes |
| Expenses, other income, service providers (Phase 2) | No | Yes | No | Yes |
| See unit costs and profit | No | Yes | Costs only | Yes |
| Products, prices, routes, targets, users | No | Prices and targets | Product list | Everything |
| Read the audit log | No | Yes | No | Yes |
| Delete a record | Nobody | Nobody | Nobody | Nobody. Records are voided with a reason. |

You confirmed these permissions on 11 Nov, including that the production manager sees unit costs (costs only, not profit).

**Sign-in.** Agents use their phone number and a PIN, locked after five wrong tries. The accountant, production manager and admin use email and password, with a second step for the accountant and admin. A lost phone is signed out remotely from the console.

## Setup before the first trip

The admin completes these steps once. Agents cannot sell until steps 1 to 8 are done.

1. **Create the company.** Name, currency (UGX, whole shillings), time zone (Africa/Kampala), logo, and the prefix used on invoice numbers.
2. **Create users and roles.** Admin, accountant, production manager, then each agent (Gloria, Winnie, Faridah) with phone number, PIN and agent code (GLO, WIN, FAR).
3. **Load the product list.** The 22 products on the Products sheet: name, size, unit price (unit price only, not carton price), category (coffee, dried fruit, honey), whether it expires, and a unit cost (optional in Phase 1, hidden from agents).
4. **Create routes.** Kampala, Eastern Route and Western Route, each marked Kampala or Upcountry.
5. **Assign agents to routes.** Gloria to Kampala, Winnie to Eastern Route, Faridah to Western Route. An agent can be moved later; old sales keep the route they were made on.
6. **Set payment methods.** Cash, MTN MoMo, Airtel Money and Bank transfer (an editable list), and the evidence each one needs.
7. **Set route targets.** One default daily target of UGX 100,000 for each route, for motivation only; it can be changed per route later.
8. **Set alert thresholds.** The defaults in the Alerts section: day-close time, "no sales by" time, stock-variance tolerance, credit limit and overdue days.
9. **Load categories.** Trip expense types (Allowances, Car repair, Fuel, Street parking, Offloading), office expense types (Lunch, Transport, Resources, Rent, Salaries, AOB) and income types. Phase 2 uses them, but they are loaded now.
10. **Load the starter client list.** The client names from the workbook (Winnie's history alone has 24 client rows), flagged for a duplicate check by the accountant.
11. **Load opening debtors.** Every credit row still owed in the workbook becomes an opening invoice with its original date and pending amount. Winnie's imported history holds 1,677,500 owed across 7 credit rows, none yet with a promised date.
12. **Create a training company.** A copy with fake data where agents practise before go-live.
13. **Install on each phone.** Open the link, choose Add to Home Screen, sign in, allow the camera, and run one practice sale with the phone in aeroplane mode.

## Starting a trip

A trip begins when the accountant or production manager issues stock to an agent, and selling switches on only after the agent confirms and the stock is locked. A trip is one agent on one route over one or more days; a Kampala outing and a week on the Eastern Route are both trips.

1. **Create the trip.** The accountant or production manager picks the agent, the route, the start date and the expected return date. Either role can do this, and the console records who did.
2. **Add the opening stock lines.** For each product, enter the units issued. The screen shows any goods the agent still holds from a previous trip, so old and new stock are not mixed up.
3. **Issue.** The trip moves to Issued. The agent is notified: "Stock issued for Eastern Route. Confirm receipt."
4. **The agent confirms.** The agent counts the goods with the issuer and taps Confirm received on the Stock screen. If a count differs, the agent taps Report difference, types a note and attaches a photo. The trip then shows Issued, disputed. The agent still cannot change any quantity; the accountant or production manager corrects the line and issues again.
5. **Lock.** After confirmation the trip becomes Locked (active) and the opening stock is read-only for the agent. Selling switches on from this point.
6. **Top-ups during the trip.** If more goods are sent, the accountant or production manager adds a dated top-up with a reason, and the agent confirms it the same way. The opening stock itself never changes.
7. **Automatic deduction.** Every saved sale reduces the agent's stock on hand, and the phone and the console show the same figure.
8. **Over-sold guard.** If an agent enters more than the stock on hand, the sale is saved as normal. Nothing is blocked and there is no warning or flag, so a real sale is never lost. The stock figure shows below zero and the count at trip end settles the difference.
9. **The trip stays active** until it is closed (next sections).

Stock on hand = opening stock + top-ups - units sold + non-expired returns +/- approved adjustments. Expired returns never go back into stock. In Phase 2 an issue also reduces the factory's finished-goods stock, so the goods are accounted for from the production line to the client.

## Selling in the field

An agent records a sale in about a minute, with or without signal, and every sale carries the agent, the trip, the client, the lines, the payment and the evidence. Agents may enter each sale as it happens or all of them at day end.

### Recording a sale

1. **Sign in** with phone number and PIN. The home screen shows today's sales against the route target, cash, credit and pending totals, and stock left.
2. **Tap New sale.** The date is today, and the agent and the active trip are filled in automatically.
3. **Choose the client.** Type part of a name or a phone number to search every Ntamba client. If the client is not found, tap Add new client and enter name, phone, area and type of outlet. If a similar name or the same phone already exists, the app asks "Is this the same client?" and creates no duplicate unless the agent answers no.
4. **Add products.** Pick a product from the searchable list and type the quantity. The price comes from the price list and the agent cannot change it. Each line shows its total, and the running total sits at the bottom. Repeat for every product.
5. **Add returned goods, if any.** Tap Add returned goods, pick the product and quantity, and mark it Non-expired or Expired. Replacement products are entered as normal lines. Non-expired goods go back into the agent's stock; expired goods do not, and are recorded as a loss.
6. **Check the review screen.** It shows gross sales, minus returns, equals the net sale for the visit.
7. **Record the payment.** Choose Paid in full, Credit or Part-paid. For anything paid, choose the method (cash, MTN MoMo, Airtel Money, bank), and for part-paid enter the amount. Any unpaid balance needs a promised payment date, so a debtor can never have a blank date again.
8. **Attach the evidence** (table below).
9. **Save.** The phone gives the sale its own unique ID and an invoice number such as 261110-GLO-0004 (date, agent code, the agent's sale number for that day). Stock on hand drops at once.
10. **Sync.** With signal, the sale reaches the server in seconds. Without signal it waits on the phone, marked orange "Waiting to sync", and sends itself when signal returns. It turns green once the server has it.

### Evidence required for each payment

| Payment | Evidence the agent attaches | Checked by |
| --- | --- | --- |
| MTN MoMo or Airtel Money | The transaction ID typed in, and a screenshot of the confirmation message | Accountant, against the mobile money statement (matched automatically in Phase 3) |
| Bank transfer or deposit | Bank name, reference number, and a photo of the slip or the transfer screen | Accountant, against the bank statement |
| Physical cash | A photo of the receipt or delivery note signed by the client. A finger signature on the phone is not accepted for cash | Accountant, by viewing the picture |
| Credit (nothing paid yet) | The client's signature or a photo of the signed delivery note, proving the goods were received on credit | Accountant |

My proposal: a sale can be saved without evidence so the agent is never stuck in the field. It then shows Evidence missing in red for the accountant, and the day cannot be closed until the evidence is attached or a reason is typed (for example, the client refused to sign).

### Taking a later payment

1. The agent taps Receive payment and finds the client. The app lists that client's open invoices from the agent's own sales.
2. Enter the amount, the method and the evidence.
3. By default the payment settles the oldest invoice first. The agent may pick another.
4. The payment is tagged to the agent who collected it and shows as an installment on the invoice.
5. A payment above the pending balance is refused.

### Correcting a mistake

1. Until the day is closed, the agent opens the sale from My sales, changes lines, payment or client, adds a reason and saves. Stock and totals recalculate.
2. Every edit keeps the old version in the history. If the accountant had already seen the sale, it is flagged Edited.
3. If the accountant corrected the same sale while the agent was offline, the office version wins and the agent sees "Changed by the office".
4. After the day is closed the agent cannot edit. A Request correction button sends a note to the accountant.

### Other things the agent can do

- **Record a visit with no sale.** Tap No sale, pick the client and a reason (closed, no money, still has stock, other). It is optional, takes seconds, and shows which outlets were visited.
- **Enter a late sale.** After days without signal or phone trouble, an agent can date sales up to three days back (a setting). They are marked Late entry.
- **See own performance.** Today and the week against the route target, top items sold, and own clients with pending balances.

## Closing the day

Each working day ends with the agent closing the day and handing over the money, and nothing for that day changes afterwards except through the accountant.

1. **Check sync.** The agent taps Close day. The app first checks that every sale and photo has reached the server; anything waiting must sync first.
2. **Fix gaps.** The app lists sales with missing evidence or missing promised dates. Each one is fixed or given a reason.
3. **Read the day summary.** Net sales, cash received by method (cash, MoMo, bank), credit given, installments collected on old invoices, returns, and stock left by product.
4. **Declare the handover.** The agent enters the physical cash being handed over and says how: at the office, deposited at a bank or mobile money agent, or sent to the company line. MoMo and bank payments already in company accounts are not counted as cash to hand over. The screen shows Expected cash = cash sales + cash installments received - cash trip expenses paid from the float (Phase 2), and the difference from the declared figure.
5. **Attach handover evidence.** For a deposit or transfer: the slip or screenshot. For a hand-in at the office, the accountant's confirmation is the evidence.
6. **Submit.** The day becomes Closed by agent, and that date's sales are locked for the agent.
7. **Accountant confirms the cash.** The accountant counts what was handed in, or checks the deposit against the bank or mobile money statement, enters the confirmed amount and confirms. Any difference is recorded as a shortage or surplus against the agent for that day, with a note.
8. **The day is Final.** The accountant can reopen a day with a reason, and the reopening is logged.
9. **Missed close.** If the day is not closed by the set time (default 21:00), the accountant gets an alert and the agent a reminder. The agent can still close next morning, and the day is flagged Late close.

**Upcountry note.** On the Eastern and Western routes a physical hand-in every day is not possible. For those routes the daily handover is a deposit to the company's bank or mobile money account, with the slip or screenshot as evidence. That matches the Bank name + Agent and Boss mobile ticks on your hand-drawn upcountry form. Please confirm this reading.

## Closing the trip

A trip closes only after the office counts the goods the agent still holds, so any difference is found while the details are fresh.

1. **End trip.** After the last day is closed, the agent taps End trip. The app shows expected closing stock per product: opening stock + top-ups - sold + non-expired returns +/- approved adjustments.
2. **Hand back the goods.** The agent returns the remaining goods, and any expired returns collected from clients, to the office or factory.
3. **Count.** The accountant or production manager counts each product and enters the counted quantity. The agent cannot enter or change the count.
4. **Read the variance.** For every product the system shows counted minus expected, in units and in UGX at selling price (and at cost in Phase 2).
5. **Decide each difference.** Zero needs nothing. A shortage is flagged against the agent, and the accountant records a decision (accepted, to be recovered, under investigation) with a note. A surplus is flagged as a possible unrecorded sale or entry error, and the accountant records the cause.
6. **Write off expired goods.** Expired returns collected are counted and recorded as a loss at cost. In Phase 2 the loss posts to the ledger.
7. **Reconcile cash.** The system adds up the trip's daily handovers and compares them with expected cash. Unpaid balances stay in Debtors.
8. **Review trip costs.** Allowances, fuel, car repair, parking and offloading entered for the trip (Phase 2) are listed beside its sales.
9. **Produce the trip report.** Sales, cash against credit against pending, returns, stock variance, trip costs and target achieved, as a screen and a PDF. This is the evidence pack for the trip.
10. **Close.** The trip becomes Closed and locked. Only the accountant or admin can reopen it, with a reason.

## Debtors and installments

A sale that is not fully paid creates an open invoice, and every later payment is an installment against it, so the pending balance is always the net sale minus the payments received.

**How the numbers work (the same as the workbook)**

- Gross sales, minus non-expired returns, minus expired returns, give the net sale.
- Net sales are counted on the day of the sale, credit included (accrual).
- Cash received is counted on the day the money arrives.
- Pending = net sale - all payments received on that invoice.

**Invoice status**

| Status | Meaning |
| --- | --- |
| Open | Nothing paid yet |
| Part-paid | Some installments received |
| Paid | Pending is zero |
| Overdue | The promised date has passed and pending is above zero |
| Written off | The accountant has given up on the balance, with a reason |

**Steps**

1. A sale with an unpaid balance requires a promised payment date.
2. Each installment is recorded with amount, method, evidence, date and the person who collected it.
3. By default a payment settles the client's oldest invoice first. The collector can choose another.
4. A payment larger than the pending balance is refused. The accountant can record the excess as an advance from the client.
5. If a client cannot pay by the promised date, the agent or accountant sets a new date. The old date is kept, so the console can show how many promises were broken.
6. The accountant's Debtors screen filters by agent, route, client, status and age band (0 to 7 days, 8 to 30, 31 to 60, over 60).
7. Each client page shows the total owed, the oldest unpaid invoice, the overdue amount, and promises kept against promises broken.
8. An optional credit limit per client warns the agent when a sale would exceed it. The accountant sets the limits; none exist until then.
9. An agent sees only the invoices from their own sales. The accountant sees them all.
10. Only the accountant or admin can write off an invoice, with a reason. Written-off invoices stay visible.

## The accountant console

The accountant sees every agent as the data arrives and can change any detail, so the accountant stops being the only person who types and becomes the person who checks.

**The live board (home screen).** One tile per agent shows:

- Status: last sync time, active trip and route, and day status (open, closed by agent, final).
- Net sales today, split into cash, credit and part-paid, plus the pending balance.
- Installments collected today.
- Progress against the route target, as a percentage.
- Stock left, with the lowest products listed.
- Red flags: evidence missing, waiting to sync.

"Real time" means seconds when a phone has signal and as soon as it reconnects when it does not. Each tile shows the phone's last sync time, so the accountant knows how fresh it is.

**Steps through the day**

1. Open the live board in the morning and see who has an active trip and who has not started.
2. Click an agent to open the agent page: today's sales, stock by product, clients visited, installments and expenses.
3. Open any sale to see its lines, payment and evidence photos. Click Verify evidence, or Reject with a note that goes back to the agent.
4. Edit any detail of any sale, payment, client or stock line. A reason is required, the old and new values are logged, and the sale shows Edited by accountant.
5. Enter a sale or payment for an agent whose phone failed. It is tagged "entered by accountant for the agent".
6. Work the inbox: evidence missing, stock differences reported, new clients to check for duplicates, edited sales, handover differences and late closes.
7. Merge duplicate clients: pick the two, choose the name that stays, and the history of both joins under one client.
8. Review clients on credit by agent, route, status and age band, sorted by overdue amount.
9. At day end, confirm each agent's cash handover (see Closing the day).
10. Review item performance: units and value sold per product, per route, per day, with slow movers marked against what the route target implies.
11. Download reports as screen, PDF or Excel.

## Finance section (Phase 2)

Phase 2 puts all of Ntamba's money in one place, so money in, money out, who owes the company and whom the company owes can be read on one screen.

### Incoming money

1. Sales receipts arrive automatically from the Phase 1 payments (cash, MoMo, bank), already split by method and agent.
2. The accountant records other income (the AOB items) with date, amount, category, method and a note against each.
3. Bank and mobile money deposits made by agents are matched to the handovers they cover.

### Office daily expenses

1. The accountant records each expense: date, category (lunch, transport, resources, rent, salaries, AOB), amount, who it was for, how it was paid (petty cash, bank, mobile money) and a receipt photo.
2. Office expenses stay separate from trip expenses (allowances, car repair, fuel, parking, offloading). Trip expenses belong to a trip and appear in its report.
3. Each expense is either Paid now or Owed. An owed expense becomes a bill from a service provider.

### People the company owes (service providers)

1. Keep one record per provider: name, phone, service and payment terms.
2. Record each bill: provider, invoice reference, amount, bill date and due date.
3. Record each payment, installments allowed: amount, method, evidence and date. The balance owed updates by itself.
4. The creditors list is sorted by due date and aged: not yet due, 1 to 30 days, 31 to 60, over 60.
5. A bill due within three days raises an alert.
6. Each provider page keeps the history of services paid.

### Cash against credit

One screen shows money in from cash sales and credit sales (accrual), money out paid now or owed, what is still to collect from clients and what is still to pay providers.

### Cash book and petty cash

1. Opening balance, money in, money out and closing balance, per day and per account: cash on hand, MTN MoMo, Airtel Money and each bank.
2. The accountant counts cash on hand, and the book shows any difference.
3. A month-end reconciliation matches the bank and mobile money statements to the book.

## Ledger and cost of production (Phase 2)

Every sale, payment, expense and stock movement posts automatically to two accounts, one debit and one credit, so the books balance without anyone typing journals. The agent never sees the ledger.

### How the ledger works

1. A chart of accounts is loaded for Ntamba (starter list below) and the accountant can edit it.
2. When an event is saved, the system creates a journal entry whose debits and credits add up to the same amount. An entry that does not balance cannot be saved.
3. Journal entries are never edited or deleted. A correction posts a reversing entry and a new entry, so the history stays readable.
4. The accountant can post manual journals for things the system does not capture, with a reason.
5. Closing a period, for example a month, locks it. Later changes need a reopen with a reason.
6. At the start of Phase 2 the system posts the whole Phase 1 history into the ledger in one run, using the rules below.
7. Reports: trial balance, profit and loss, a simple balance sheet, the cash book, and debtors and creditors, each agreeing with its control account.

**Starter chart of accounts**

| Type | Accounts |
| --- | --- |
| Assets | Cash on hand; Cash with agents (not yet handed in); MTN MoMo; Airtel Money; Bank accounts; Debtors; Finished goods stock; Raw materials and packaging stock |
| Liabilities | Creditors (service providers); Accruals; Advances from clients |
| Equity | Capital; Retained earnings |
| Income | Sales; Sales returns (reduces sales); Other income |
| Cost of sales | Cost of goods sold; Expired goods loss; Stock variance loss; Production overheads absorbed |
| Expenses | Trip expenses (Allowances, Car repair, Fuel, Street parking, Offloading); Office expenses (Lunch, Transport, Resources, Rent, Salaries, AOB); Cash shortages |

**Posting rules** (amounts are examples)

| # | Event | Debit | Credit |
| --- | --- | --- | --- |
| 1 | Cash sale of 100,000 | Cash with agents 100,000 | Sales 100,000 |
| 2 | Credit sale of 100,000 | Debtors 100,000 | Sales 100,000 |
| 3 | Sale of 100,000, 30,000 paid by MoMo | MTN MoMo 30,000; Debtors 70,000 | Sales 100,000 |
| 4 | Installment of 20,000 received in cash | Cash with agents 20,000 | Debtors 20,000 |
| 5 | Daily handover of 120,000 in cash | Cash on hand 120,000 | Cash with agents 120,000 |
| 6 | Handover of 115,000 against 120,000 expected | Cash on hand 115,000; Cash shortages 5,000 | Cash with agents 120,000 |
| 7 | Non-expired return worth 10,000 | Sales returns 10,000; Finished goods stock (at cost) | Debtors 10,000; Cost of goods sold (at cost) |
| 8 | Expired return worth 10,000 | Sales returns 10,000; Expired goods loss (at cost) | Debtors 10,000; Cost of goods sold (at cost) |
| 9 | Cost of the goods in each sale | Cost of goods sold (units x unit cost) | Finished goods stock |
| 10 | Stock shortage found at trip close | Stock variance loss (at cost) | Finished goods stock |
| 11 | Office expense of 15,000 paid in cash | Office expense (its category) | Cash on hand |
| 12 | Office expense not yet paid | Office expense (its category) | Creditors |
| 13 | Payment to a service provider | Creditors | Cash on hand, bank or MoMo |
| 14 | Trip expense, fuel 80,000 in cash | Trip expenses (Fuel) | Cash on hand |
| 15 | Other income of 50,000 by bank | Bank | Other income |
| 16 | Production batch completed | Finished goods stock (batch cost) | Raw materials and packaging stock; Production overheads absorbed |

### Cost of production

This replaces the workbook rule that profit equals net sales, now that you hold the cost details.

1. **Recipes.** For each product, list its ingredients and packaging per unit or per batch (for example kilograms of green coffee, fruit or honey, the pouch, the label and a share of the carton).
2. **Material prices.** Enter purchase prices with an effective date. A new price affects future batches only.
3. **Labour and overheads.** Enter direct labour per batch and overhead rates (power, roasting or drying, rent share) per kilogram or per batch.
4. **Record each batch.** Product, date, units produced, materials actually used and waste.
5. **Unit cost** = (materials + packaging + labour + overheads) / good units. The system keeps a weighted average cost per product, updated after every batch.
6. **Factory stock.** Batches increase finished-goods stock at cost; trip issues decrease it, so trip stock is valued at cost.
7. **Gross profit.** Each sale takes its cost of goods sold from the unit cost at that time. Gross profit = net sales - cost of goods sold, shown per sale, product, agent, route and period.
8. **Price check.** An alert fires when a selling price falls below cost plus a margin you set.
9. **Until then,** Phase 1 stores one flat unit cost per product, taken from the cost list you hold. That is enough for a first gross margin report.

## Screens

The agent app has 12 screens, all in Phase 1. The accountant console has 16 in Phase 1, with the finance, ledger and costing screens added in Phase 2 and the import and messaging screens in Phase 3.

### Agent app (phone)

| # | Screen | What it does |
| --- | --- | --- |
| 1 | Sign in | Phone number and PIN, locked after five wrong tries, automatic sign-out after inactivity |
| 2 | Home | Today's sales against the target, cash, credit and pending totals, stock left, sync status, Close day button |
| 3 | My stock | Opening stock (read-only), top-ups, sold, returns and on hand per product; Confirm received; Report difference |
| 4 | New sale | Client, products, returned goods, payment, evidence, review, save |
| 5 | Clients | Search every client, view history, add a new client with the duplicate check |
| 6 | Receive payment | Find a client, see own open invoices, enter amount, method and evidence |
| 7 | My sales | List by date; open to edit until the day is closed; Request correction afterwards |
| 8 | No sale | Record a visit with no sale and the reason |
| 9 | Close day | Sync check, gaps, day summary, handover declaration, evidence, submit |
| 10 | My performance | Day and week against the route target, top items sold, own clients with pending balances |
| 11 | Sync status | What is waiting to send, last sync time, retry |
| 12 | Help | One-page guide and a button to message the accountant |

### Accountant console (computer)

| # | Screen | Phase | What it does |
| --- | --- | --- | --- |
| 1 | Live board | 1 | One tile per agent with sales, cash, credit, pending, target progress, stock and flags |
| 2 | Agent page | 1 | All of one agent's sales, stock, clients, installments and day status |
| 3 | Sales | 1 | Every sale with filters, editing with a reason and an evidence viewer |
| 4 | Clients | 1 | Client list, history, merge duplicates |
| 5 | Debtors | 1 | Open invoices, age bands, promised dates, write-off |
| 6 | Payments and evidence | 1 | Verify or reject evidence, see unverified payments |
| 7 | Trips and stock issue | 1 | Create a trip, issue stock, top up, adjust, count at trip end |
| 8 | Stock | 1 | Stock on hand by agent and by product, movements, variances |
| 9 | Day close | 1 | Confirm each agent's cash handover, record differences, reopen a day |
| 10 | Alerts inbox | 1 | All open alerts, assign, resolve |
| 11 | Targets | 1 | Route targets, daily and weekly |
| 12 | Reports | 1 | Every report with PDF and Excel export |
| 13 | Products and prices | 1 | Product list, unit price with history, unit cost |
| 14 | Users and devices | 1 | Create users, reset PINs, sign a phone out |
| 15 | Audit log | 1 | Every change, who made it, when, old and new value, reason |
| 16 | Settings | 1 | Payment methods, categories, thresholds, labels |
| 17 | Expenses | 2 | Office expenses and trip expenses with receipts |
| 18 | Other income | 2 | The AOB items |
| 19 | Service providers | 2 | Providers, bills, payments, balances owed |
| 20 | Cash book | 2 | Daily money in and out per account, reconciliation |
| 21 | Chart of accounts and journal | 2 | Accounts, automatic and manual journals, period lock |
| 22 | Financial reports | 2 | Trial balance, profit and loss, balance sheet |
| 23 | Costing | 2 | Recipes, material prices, batches, unit cost, margins |
| 24 | Factory stock | 2 | Finished goods and materials on hand |
| 25 | Import wizard | 3 | Upload, map, preview, approve, roll back |
| 26 | Connections | 3 | Zoho and other sources |
| 27 | Messages | 3 | SMS and WhatsApp reminders |
| 28 | Statement matching | 3 | Match mobile money and bank statements to payments |

## Data tables

Stock, money and audit history sit in a small set of tables, and every table carries a company id so the same system can later serve another workspace. Records are voided, never deleted, and each has created by, created at and updated at.

### Phase 1

| Table | Holds | Key fields |
| --- | --- | --- |
| companies | One row per workspace | name, currency, time zone, settings |
| users | Agents, accountant, production manager, admin | name, phone, email, role, agent code, PIN or password hash, active |
| devices | Each phone an agent signs in on | user, device name, last seen, signed out flag |
| routes | Kampala, Eastern Route, Western Route | name, zone type, active |
| products | The price list | code, name, size, category, tracks expiry, unit price, unit cost, active, external id |
| price\_history | Past prices, so old sales keep the price they were made at | product, price, effective from |
| clients | Every customer | name, phone, area, outlet type, merged into, external id |
| trips | One agent on one route | agent, route, start, expected end, status (draft, issued, locked, closing, closed) |
| stock\_movements | The single record of all stock | trip, product, type (issue, top-up, sale, non-expired return, expired return, adjustment, count), quantity, reference, date |
| sales | One row per client visit with a sale | unique ID from the phone, invoice number, agent, trip, client, sale date, gross, non-expired returns, expired returns, net, paid, pending, promised date, status, entered by, device, synced at, version |
| sale\_lines | The products on a sale | sale, product, quantity, unit price, line total, kind (sold, returned non-expired, returned expired) |
| payments | Every payment and installment | unique ID, client, amount, method, reference, received by, received at, evidence status |
| payment\_allocations | Which invoice each payment settles | payment, sale, amount |
| evidence\_files | Photos, screenshots, signatures | owner type, owner id, file, kind, uploaded by, status (pending, verified, rejected), verified by |
| day\_closes | Daily close and handover | agent, date, status, expected cash, declared cash, confirmed cash, difference, handover method, evidence, confirmed by |
| visits | Visits with no sale | agent, client, date, reason |
| targets | Route targets | route, period, value target, units target per product |
| alerts | Raised alerts | type, severity, subject, status, assigned to, resolved by |
| audit\_log | Every change | user, action, table, record, before, after, reason, device, time |

### Phase 2

| Table | Holds |
| --- | --- |
| accounts | The chart of accounts |
| journal\_entries, journal\_lines | Balanced debit and credit lines, with the event that created them |
| periods | Open and locked accounting periods |
| expense\_categories, expenses | Office and trip expenses, with receipts and paid or owed |
| other\_income | The AOB items |
| providers, provider\_bills, provider\_payments | Service providers, what is owed and what was paid |
| cash\_accounts, cash\_book\_entries | Cash on hand, mobile money and bank accounts and their daily movement |
| recipes, recipe\_lines | Ingredients and packaging per product |
| material\_prices | Purchase prices with effective dates |
| batches | Production batches, units made, materials used, waste |
| cost\_history | Unit cost per product over time |

### Phase 3

| Table | Holds |
| --- | --- |
| import\_batches, import\_rows | Each upload, its rows, mapping, errors and rollback state |
| external\_links | Maps Ntamba records to Zoho or other system IDs |
| messages | SMS and WhatsApp sent, status |
| statement\_lines | Imported mobile money and bank statement lines and their matches |

**On the phone only:** a local copy of the price list, client search list, the agent's own trip stock and the last 30 days of the agent's own sales, plus the sync queue of items waiting to send.

## Business rules

The system enforces these rules itself, so they do not depend on anyone remembering them. Rules 2 and 3 are the workbook's own logic carried over.

| # | Area | Rule |
| --- | --- | --- |
| 1 | Money | Each line takes the price on the price list on the sale date and stores it, so a later price change never alters an old sale. Agents cannot change prices. |
| 2 | Money | Net sale = gross sales - non-expired returns - expired returns, counted on the sale date, credit included. |
| 3 | Money | Cash is counted on the day it is received. Pending = net sale - payments received on that invoice. |
| 4 | Money | Any unpaid balance needs a promised payment date. |
| 5 | Money | A payment cannot exceed the pending balance. The accountant can record any excess as an advance. |
| 6 | Money | All amounts are whole Uganda shillings, with no decimals. |
| 7 | Money | An invoice number is the date, the agent code and the agent's sale number for that day, for example 261110-GLO-0004. Numbers are never reused. Invoice numbers from the workbook are kept as a reference. |
| 8 | Stock | Stock on hand = opening stock + top-ups - units sold + non-expired returns +/- approved adjustments. |
| 9 | Stock | Expired returns never go back into stock. They are recorded as a loss. |
| 10 | Stock | Only the accountant or production manager creates opening stock and top-ups. The agent confirms. After confirmation the lines are locked, and changes are new dated lines with a reason. |
| 11 | Stock | Selling is possible only on a locked trip. A sale that takes stock below zero is saved with no flag, never refused. |
| 12 | Stock | A trip cannot close until the office has entered the count. |
| 13 | Stock | Stock movements are never edited. A correction is a new movement. |
| 14 | Evidence | Every payment needs evidence for its method. A sale can be saved without it but is flagged, and the day cannot close until it is attached or explained. |
| 15 | Evidence | The accountant verifies or rejects evidence. A rejected item goes back to the agent with a note. |
| 16 | Editing | An agent edits own sales until the day is closed. The accountant and admin edit any record at any time, with a reason. |
| 17 | Editing | Records are voided with a reason, never deleted. Voiding a sale returns its stock. |
| 18 | Editing | Every create, edit, void and reopen is logged with old value, new value, user, device, time and reason. |
| 19 | Day | A day closes only when everything has synced, the handover is declared and the gaps are fixed. The accountant then confirms the cash. |
| 20 | Day | Agents cannot date a sale in the future, or more than three days back (a setting). Older entries are marked Late entry. |
| 21 | Access | An agent sees own sales, payments, stock and invoices. An agent can search every client's name, phone and area, but not other agents' sales or balances. |
| 22 | Access | Unit costs and profit are hidden from agents. |
| 23 | Clients | A similar name or the same phone number triggers a "same client?" prompt. Only the accountant merges clients, and the merged history is kept. |
| 24 | Sync | Every sale, payment and photo has a unique ID made on the phone, so sending it twice never creates a copy. |
| 25 | Sync | If the office edited a record while the phone was offline, the office version wins and the agent is told. |
| 26 | Sync | Times are stored in UTC and shown in Africa/Kampala. |
| 27 | Workspaces | Every record carries a company id, and names, routes, products and labels come from settings, not code. |
| 28 | Ledger (Phase 2) | Every journal entry balances. Entries are never edited. A correction posts a reversal and a new entry. |
| 29 | Ledger (Phase 2) | A locked period cannot change until it is reopened with a reason. |
| 30 | Costing (Phase 2) | Cost of goods sold uses the unit cost at the time of the sale. |

## Alerts and reports

The accountant gets 14 alerts in Phase 1, each with a default threshold you can change in Settings, and every report in the old workbook has a replacement.

### Alerts

| Alert | Fires when (default) | Goes to | Phase |
| --- | --- | --- | --- |
| Evidence missing | A payment has no evidence 2 hours after it syncs, or at day close | Accountant | 1 |
| Day not closed | The agent has not closed the day by 21:00 | Accountant, agent | 1 |
| No sales yet | An agent has an active trip but no sale or visit by 12:00 | Accountant | 1 |
| Behind target | Off by default, because targets are not enforced. When on: at 15:00 the day's net sales are below 50% of the route target | Accountant | 1 |
| Low stock | A product falls below 20% of what was issued, or reaches zero | Accountant, agent | 1 |
| Over-sold | Off by default, because over-sold sales are saved without a flag. When on: a sale takes stock below zero | Accountant | 1 |
| Slow-moving item | Halfway through the planned trip days, a product has sold under 25% of the units issued | Accountant, production manager | 1 |
| Stock variance | The count at trip end differs from expected by more than the tolerance | Accountant, production manager | 1 |
| Large credit | Off while no credit limits are set. When on: one sale's unpaid balance is above the credit limit | Accountant | 1 |
| Overdue promise | A promised date has passed and the balance is still above zero | Accountant, agent | 1 |
| Handover difference | Declared cash differs from expected, or confirmed cash differs from declared | Accountant | 1 |
| Edited after sync or close | A sale changes after the accountant could have seen it | Accountant | 1 |
| Possible duplicate client | A new client resembles an existing one | Accountant | 1 |
| Sync stuck | A phone has items waiting for over 1 hour | Accountant, agent | 1 |
| Bill due | A provider bill is due within 3 days | Accountant | 2 |
| Price below cost | A selling price is under cost plus the set margin | Accountant | 2 |
| Reminders by SMS or WhatsApp | Overdue promises and day-close reminders sent as messages | Agent, later the client | 3 |

Alerts appear in the console inbox and the bell. Where the phone supports it, they also arrive as push notifications.

### Reports

| Report | For | What it shows | Phase |
| --- | --- | --- | --- |
| Agent daily sheet | Agent (own), accountant | Every sale of the day with lines, payment and pending. Replaces each agent tab. | 1 |
| Agent performance | Accountant | Net sales, cash, credit, pending, installments, items sold and target percentage by day, week and month | 1 |
| Route report | Accountant | Kampala against Eastern and Western: sales, cash, credit, stock and target. Replaces the Route Report. | 1 |
| Summary and business report | Accountant, admin | Day, week and period totals of net sales, cash received, credit and pending. Replaces the Summary, Period and Business Reports. | 1 |
| Debtors aging | Accountant | By agent, route, client and age band, with promises kept and broken | 1 |
| Client statement | Accountant, agent (own) | Invoices and payments for one client | 1 |
| Stock report | Accountant, production manager | Per trip: opening, top-ups, sold, returns, expected, counted, variance | 1 |
| Item performance | Accountant, production manager | Units and value by product, route and day, with slow movers | 1 |
| Cash handover report | Accountant | Declared against confirmed per agent per day, with shortages | 1 |
| Trip report | Accountant, agent (own) | The evidence pack for a trip | 1 |
| Expenses by category | Accountant | Office and trip expenses by period | 2 |
| Creditors aging | Accountant | What is owed to providers, by due date | 2 |
| Cash book | Accountant | Money in and out per account per day | 2 |
| Trial balance, profit and loss, balance sheet | Accountant, admin | The financial statements | 2 |
| Gross margin | Accountant, admin | By product, agent, route and period | 2 |
| Cost sheet | Production manager, accountant | Cost breakdown per product | 2 |

Every report exports to PDF and Excel.

## Technical design

You agreed with the recommended stack, so this section records how each part is built. One Laravel back end serves an offline-first agent app and an accountant console from a single code base.

- **Stack.** Laravel (the latest stable release when the build starts) with PostgreSQL or MySQL. I lean to PostgreSQL for its strict rules and JSON settings; MySQL is fine if your hosting already offers it.
- **One code base, two faces.** A Laravel API with token sign-in, and one Vue 3 front end built with Vite and a PWA plugin: a phone layout for the agent app and a desktop layout for the console, the same sign-in, and routes by role.
- **Offline-first agent app.** A service worker keeps the app itself on the phone. A local database (IndexedDB) holds the price list, the client search list, the agent's own trip stock and own recent sales. Every action is written to a local queue first, then sent in order, retrying with back-off. Each queued item shows waiting, sent or failed.
- **Idempotent sync.** Every sale, payment and photo gets a unique ID on the phone, and the server saves by that ID, so a resend never makes a copy. Each record has a version number. If the office edited a record while the phone was offline, the office version wins and the agent is told.
- **Photos.** Taken with the phone camera, shrunk on the phone to a few hundred kilobytes, uploaded after the sale data, and stored privately. The console opens them through links that expire.
- **Live console.** Updates arrive by WebSocket (Laravel Reverb) with 20-second polling as a fallback. Each tile shows the phone's last sync time.
- **Notifications.** An in-app bell, plus web push where the phone supports it (an iPhone needs the app added to the Home Screen and iOS 16.4 or newer). SMS and WhatsApp come in Phase 3.
- **Android and iPhone.** Agents install from the browser with Add to Home Screen. On iPhone this is required, not optional: Safari can clear stored website data for sites not opened for about a week, and an installed Home Screen app is exempt.
- **Security.** HTTPS only; PIN lockout; a device list with remote sign-out that also clears the phone's local data; roles checked on every request and covered by tests; rate limits; hashed passwords; a second sign-in step for the accountant and admin; private evidence storage; the audit log.
- **Hosting and backups.** One VPS to start (about 2 vCPU, 4 GB memory, SSD) in a region with good speed to Kampala. A daily database backup kept for 30 days, a weekly full copy stored off the server, and a restore test every month. Uptime and error monitoring, and a staging copy for training.
- **Quality.** Automated tests on everything that moves money or stock (net sales, installments, stock on hand, day close, ledger balance), loaded with the workbook's real numbers so the system must reproduce them. Tagged releases and database changes kept in code.
- **Customisable for other workspaces.** A company id on every record, enforced by a global scope. Products, routes, payment methods, expense types, thresholds, labels, logo and invoice prefix are settings per company. No company or route name is written into the code. One shared database to begin with; a separate database per company later if a client needs it.
- **Ready for imports.** An `external_id` on clients, products, invoices, payments, providers and bills. The Phase 3 import module maps columns, previews, flags duplicates and can roll back a whole batch.
- **Personal data.** Client names and phone numbers are personal data under Uganda's Data Protection and Privacy Act, 2019. Keep access limited by role, and take legal advice on registration and on the notice you give clients. I am not a lawyer.

## Build phases, step by step

The build runs in four stages, each ending in a gate you sign off before the next begins. Durations are rough planning figures for one full-time developer. You are building with an AI full-stack builder, so read them as the size and order of the work, not as dates; they are firmed up after Phase 0.

&#91;embedded content: roadmap · four phases, three gates\]

Each diamond is a gate: you sign off before the next phase starts.

| Phase | Goal | Rough duration | Gate to pass |
| --- | --- | --- | --- |
| 0 Preparation | Fix the rules and the data before any code | 1 to 2 weeks | The questions in the last section are answered and the screens approved |
| 1 Sales and stock | Agents sell on phones, the accountant watches live, stock and cash are controlled | 10 to 14 weeks | Ten working days of parallel running in which the system matches the workbook to the shilling |
| 2 Finance and ledger | Expenses, income, service providers, the double-entry ledger, cost of production | 8 to 10 weeks | The trial balance balances, and a closed month reconciles to bank and mobile money |
| 3 Imports and messaging | Zoho and other imports, SMS and WhatsApp, statement matching | 4 to 6 weeks | A test import is clean and can be rolled back |

Every step is delivered the same way: build it, test it against the workbook's numbers, demonstrate it to you, fix what you find, and sign it off before the next.

### Phase 0: Preparation

1. You answer the questions in the last section.
2. I produce phone screen mock-ups of the sale flow, stock confirmation and day close, for you and one agent to try on paper.
3. I produce the Phase 1 database design and a test-case checklist built from the workbook.
4. We decide hosting, the domain name, and who develops and maintains the system.
5. You collect the cost data and recipes. They are needed for Phase 2 and do not block Phase 1.

### Phase 1: Sales and stock

| Step | What is built | Done when |
| --- | --- | --- |
| M1 Foundation | Project setup, environments, sign-in, roles, company scoping, audit log, settings, users, devices, products and prices, routes | The admin signs in, creates users and products, and the audit log records each change |
| M2 Stock engine | Trips, stock issue, confirm and lock, top-ups, stock movements, stock on hand | The accountant issues stock, the agent confirms on a phone and cannot edit it, and on-hand figures match a hand count |
| M3 Clients | Client records, search, duplicate check, merge, loading the starter list | The workbook's clients are loaded and a deliberate duplicate is caught |
| M4 Sales capture | The full sale flow, returns, payments, promised dates, invoice numbers, stock deduction, edit until close, void | A day from the workbook, entered by hand, gives the same net sales, cash and pending as the workbook |
| M5 Offline and sync | Local store, queue, unique IDs, the conflict rule, photo upload, install as an app | 20 sales entered in aeroplane mode all arrive exactly once after signal returns |
| M6 Evidence, payments, debtors | Evidence capture per method, verification screen, receive payment, installments, debtors view and age bands | A part-paid invoice settled in three installments shows the right balance at each step |
| M7 Day close and handover | Close day, expected against declared cash, accountant confirmation, day lock, reopen | A day with a deliberate 5,000 shortage is caught and recorded against the agent |
| M8 Console, targets, alerts, reports | Live board, agent pages, edit with reason, route targets, the 14 alerts, the 10 Phase 1 reports and exports | Every report reproduces the workbook's figures for the same data |
| M9 Pilot and go-live | One agent first (Kampala), ten working days beside the workbook, then the other two agents one at a time | Daily totals match for 10 working days and the accountant signs off |

### Phase 2: Finance and ledger

| Step | What is built | Done when |
| --- | --- | --- |
| P2.1 Ledger core | Chart of accounts, posting engine, journals, periods, posting the Phase 1 history | The trial balance balances for the whole Phase 1 history |
| P2.2 Expenses and income | Office and trip expenses, other income, receipts, paid or owed | Every expense appears in the ledger and the cash book |
| P2.3 Service providers | Providers, bills, installments, creditors aging, due alerts | The creditors list equals the creditors account |
| P2.4 Cash book and reconciliation | Accounts, daily movement, cash counts, statement reconciliation | One month's cash book agrees with the bank and mobile money statements |
| P2.5 Costing | Recipes, material prices, batches, unit cost, factory stock, cost of goods sold | Unit cost for three products agrees with your own hand calculation |
| P2.6 Financial reports | Trial balance, profit and loss, balance sheet, gross margin, period lock | A closed month's profit and loss agrees with the accountant's own |
| P2.7 Parallel run | One month beside the accountant's current method | Month-end figures match and the accountant signs off |

### Phase 3: Imports and messaging

| Step | What is built | Done when |
| --- | --- | --- |
| P3.1 Import module | Upload, map columns, preview, duplicate check, approve, roll back; clients, products, opening balances and history | A test import is clean and a whole batch can be rolled back |
| P3.2 Zoho connection | Direct sync if Zoho's interface suits, otherwise a scheduled file import. Sources: Zoho Books, Zoho Inventory, Zoho CRM and the piclesale.com records | A record created in one system appears in the other |
| P3.3 Messages | SMS and WhatsApp reminders to agents, later to clients, and payment confirmations | Reminders arrive and are logged |
| P3.4 Statement matching | Match mobile money and bank statement lines to payments | Most of a month's lines match unaided and the rest are listed for review |
| P3.5 Second workspace | Create a new company, copy settings, separate users | A test company runs without seeing any Ntamba data |

## Moving from Excel

The workbook stays the system of record until the system passes its Phase 1 gate, and then it is archived. Each sheet has a home in the new system.

| Workbook sheet | Where it goes |
| --- | --- |
| Sales GLORIA, Sales WINNIE, Sales FARIDAH | Sales and sale lines, one login per agent. The product pop-up macro is retired. |
| Products | Products and price history |
| Debtors (automatic) | The Debtors view: invoices with pending above zero |
| Debtor Payments | Payments and installments |
| Stock | Trips, stock issues and stock movements |
| Kampala Expenses, Upcountry Expenses | Trip expenses (Phase 2) |
| Company Expenditure | Office expenses (Phase 2) |
| Other Income | Other income (Phase 2) |
| Service Providers, Provider Payments | Providers, bills and payments (Phase 2) |
| Summary, Period Report, Business Report, Route Report | Reports |
| Lists | Settings |
| Guide | The Help screen |

**Steps**

1. Keep the workbook as the system of record until the system passes its gate.
2. Export the starter data from the workbook with a script: products, clients, open invoices and current stock per agent.
3. Choose a cut-over date at the start of a week. Load the opening balances: open invoices, opening stock as the first issue for each agent, and cash balances.
4. Run one agent (Kampala) in the system for ten working days while the accountant keeps that agent's rows in the workbook too. Using one agent keeps the double typing small.
5. Each evening compare the system's daily report with the workbook for that agent: net sales, cash received, pending and stock on hand. Investigate every difference.
6. When ten days match, bring in the Eastern Route agent, and a week later the Western Route agent.
7. When all three are live, save the workbook as read-only and retire the macro.
8. Load only open items and opening balances at cut-over. Older history stays in the archived workbook until the Phase 3 import brings it in.

## Risks

The biggest risk is cash that is sold but never handed over, and the design answers it with daily handover, evidence and verification. The others are listed with their safeguards.

| Risk | Why it matters | Safeguard |
| --- | --- | --- |
| Poor signal on routes | Eastern and Western routes lose signal often | The app works offline and syncs later. Every screen an agent needs is on the phone, and sync status is always visible. |
| Cash sold but not handed in, or fake evidence | The system knows what was sold, not what was banked | Daily handover, evidence per payment, accountant verification, handover difference alerts, and mobile money and bank matching in Phase 3. A photo can still be faked, so matching statements is the real check. |
| Agents skip or delay entries | Live figures are only as good as the entries | Alerts for no sales, behind target, day not closed and sync stuck. This reduces the problem but cannot remove it. |
| Own phones | Loss, theft, an agent leaving, and data bundles | PIN lock, remote sign-out that clears local data, a device list, small payloads and compressed photos. Whether Ntamba pays a data allowance is a business decision. |
| Duplicate or misspelt clients | The workbook's invoice numbers already suffered from spelling | A same-client prompt, a phone number check, and the accountant's merge tool. |
| Scope growth | Phase 1 alone is a real product | Phase gates, a written change list, and finance kept out of Phase 1. |
| Accountant overload during the parallel run | Double work for a few weeks | One agent only, ten working days, and daily comparison reports. |
| Ledger errors | Wrong postings are hard to spot later | Balanced entries enforced, no edits to journals, reversals, tests built from your numbers, and a trial balance check after each posting run. |
| Inaccurate costs | Margins would be wrong | Cost changes carry effective dates and history, and you confirm the first cost sheets by hand. |
| One person holds all the knowledge | The system stalls if that person is away | Written handover notes, a second admin account, and credentials kept in a password manager. |
| Client personal data | Names and phone numbers are protected data | Role-based access, no public file links, and legal advice on registration and consent wording. |
| Agent adoption | A system that is slow or confusing gets bypassed | Short screens, a practice company, a one-hour training session and a one-page guide. |

## Your answers, and what I still need

You answered all 16 questions on 11 Nov. Where I had to read an answer between the lines, the table says so, so correct me if I misread. What is still to be sent is listed under the table.

| # | Question | Your answer | What it changes |
| --- | --- | --- | --- |
| 1 | Evidence rules | Taken as confirmed: evidence for every payment, and a signed note for credit. A client's finger signature on the phone is not accepted for cash. | Cash needs a photo of the signed receipt or delivery note. Changed in the evidence table. |
| 2 | Upcountry handover | You gave deposit accounts (question 7) and did not object, so I took the daily handover as a bank or mobile money deposit with a slip. | No change, unless I misread. |
| 3 | Cost data | The cost you hold is both a purchase price and a production cost. Selling prices are set with production costs built in. | Phase 1 stores both costs per product. Packaging, labour, overheads, recipes and batch yields are not answered yet. |
| 4 | Route targets | No enforced targets. A default daily target of UGX 100,000, for motivation only. | One default target on every route, shown as progress and never enforced. The Behind target alert is off by default. |
| 5 | Who builds it | You are building with an AI full-stack builder and need a locked guide that gives it the whole picture. | Each phase is written as fixed, ordered instructions. Durations show the size of the work, not dates. |
| 6 | Permissions | The production manager can see costs. | Confirmed in the permission table: unit costs yes, profit no. |
| 7 | Payment methods and accounts | Cash, MTN and Airtel mobile money, and bank deposit. Banks: Pearl Bank and Centenary Bank. Mobile money merchant accounts: Airtel and MTN. | Loaded as money accounts in Phase 1. The account numbers you sent are entered on the admin screen at setup and are not written in this guide. |
| 8 | Back-dating and close time | Yes: three days of back-dating and a 21:00 close. | These are already the defaults. No change. |
| 9 | Credit limits | None for now. | The limit field stays empty. The Large credit alert is off while no limits are set. |
| 10 | Client visibility | Yes, an agent can see it. | Searching a client shows an Overdue balance marker when another agent's sale to that client is overdue. The amount stays hidden, as in the question. |
| 11 | Over-sold sales | "Do nothing." I read this as: save the sale, no block, no flag. | No warning or flag. Stock shows below zero and the trip-end count settles it. The Over-sold alert is off by default. Tell me if you wanted a flag after all. |
| 12 | Zoho | Zoho Books, Zoho Inventory and Zoho CRM, plus the piclesale.com records. | Phase 3 imports from all four. A sample export is still to be sent. |
| 13 | Accounting set-up | Yes. Not sent yet. | The chart of accounts, financial year start and opening balances seed the Phase 2 ledger. |
| 14 | Chewable 15 g or 10 g | Chewable is 10 g. A separate 15 g ground coffee sachet also exists. | Both stay in the product list: Chewable 10 g and Coffee Sachet 15 g. |
| 15 | Client consent | No consent wording is needed. | No consent screen is built. The legal-advice step in the Risks section stays as advice only. |
| 16 | Name and look | The system is called Ntamba Accounts Plus. | Used as the app name. The logo and colours are still to be sent. |

Still to be sent: the chart of accounts, financial year start and opening balances (13); a sample export from Zoho Books, Inventory and CRM (12); the logo and colours (16); and the client, vendor and item lists. None of them stops Phase 0, which starts with the phone mock-ups and the Phase 1 database design.
