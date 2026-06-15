# UI/UX BLUEPRINT

**Status:** ✅ FINAL  
**Date:** 2026-06-14  
**Target:** Android Mobile First (360px - 430px)  
**Platform:** Task Marketplace PWA

---

## 1. DESIGN SYSTEM

### Design Principles

| Principle | Description |
|-----------|-------------|
| **Clean** | Minimal visual noise, focus on content |
| **Fast** | Minimal clicks to complete tasks |
| **Task-Focused** | Every element serves the task workflow |
| **Beginner-Friendly** | Simple language, clear actions |
| **Trustworthy** | Professional feel for financial transactions |

### Design Tokens

#### Spacing Scale

| Token | Value | Usage |
|-------|-------|-------|
| `space-1` | 4px | Icon padding, micro spacing |
| `space-2` | 8px | Inline elements, tight spacing |
| `space-3` | 12px | Input padding, small gaps |
| `space-4` | 16px | Card padding, standard gaps |
| `space-5` | 20px | Section spacing |
| `space-6` | 24px | Large gaps |
| `space-8` | 32px | Section separation |
| `space-10` | 40px | Page sections |
| `space-12` | 48px | Major sections |

#### Border Radius

| Token | Value | Usage |
|-------|-------|-------|
| `radius-sm` | 6px | Badges, small elements |
| `radius-md` | 8px | Buttons, inputs |
| `radius-lg` | 12px | Cards, modals |
| `radius-xl` | 16px | Bottom sheets |
| `radius-full` | 9999px | Avatar, pills |

#### Shadow Scale

| Token | Value | Usage |
|-------|-------|-------|
| `shadow-sm` | 0 1px 2px rgba(0,0,0,0.05) | Subtle elevation |
| `shadow-md` | 0 4px 6px rgba(0,0,0,0.07) | Cards |
| `shadow-lg` | 0 10px 15px rgba(0,0,0,0.1) | Dropdowns, modals |
| `shadow-xl` | 0 20px 25px rgba(0,0,0,0.15) | Bottom sheets |

---

## 2. COLOR SYSTEM

### Primary Colors

| Color | Hex | Usage | Reason |
|-------|-----|-------|--------|
| **Primary** | `#3B82F6` | Buttons, links, active states | Blue conveys trust and reliability |
| **Primary Dark** | `#2563EB` | Hover states | Deeper blue for interaction |
| **Primary Light** | `#DBEAFE` | Backgrounds, badges | Soft blue for subtle highlights |

### Secondary Colors

| Color | Hex | Usage | Reason |
|-------|-----|-------|--------|
| **Secondary** | `#6B7280` | Secondary buttons, muted text | Neutral, doesn't compete with primary |
| **Secondary Dark** | `#4B5563` | Hover states | Darker gray for interaction |

### Status Colors

| Color | Hex | Usage | Reason |
|-------|-----|-------|--------|
| **Success** | `#10B981` | Completed, success states | Green = positive outcome |
| **Warning** | `#F59E0B` | Pending, attention needed | Yellow = caution |
| **Danger** | `#EF4444` | Errors, cancellations | Red = stop/danger |
| **Info** | `#3B82F6` | Information, neutral status | Blue = informational |

### Neutral Colors

| Color | Hex | Usage |
|-------|-----|-------|
| **White** | `#FFFFFF` | Backgrounds, cards |
| **Gray 50** | `#F9FAFB` | Page background |
| **Gray 100** | `#F3F4F6` | Secondary backgrounds |
| **Gray 200** | `#E5E7EB` | Borders, dividers |
| **Gray 300** | `#D1D5DB` | Disabled states |
| **Gray 400** | `#9CA3AF` | Placeholder text |
| **Gray 500** | `#6B7280` | Secondary text |
| **Gray 600** | `#4B5563` | Body text |
| **Gray 700** | `#374151` | Headings |
| **Gray 800** | `#1F2937` | Primary text |
| **Gray 900** | `#111827` | Emphasized text |

---

## 3. TYPOGRAPHY

### Font Family

| Type | Font | Fallback |
|------|------|----------|
| **Primary** | Inter | system-ui, -apple-system, sans-serif |

**Reason:** Inter is modern, highly readable on mobile, supports Indonesian characters well.

### Type Scale

| Name | Size | Weight | Line Height | Usage |
|------|------|--------|-------------|-------|
| **H1** | 24px | 700 | 32px | Page titles |
| **H2** | 20px | 600 | 28px | Section headers |
| **H3** | 16px | 600 | 24px | Card titles |
| **H4** | 14px | 600 | 20px | Subsection headers |
| **Body** | 14px | 400 | 20px | Default text |
| **Body Small** | 13px | 400 | 18px | Secondary text |
| **Caption** | 12px | 400 | 16px | Labels, timestamps |
| **Button** | 14px | 500 | 20px | Button text |
| **Button Small** | 13px | 500 | 18px | Small buttons |

---

## 4. COMPONENT RULES

### Button

| Variant | Background | Text | Border | Usage |
|---------|------------|------|--------|-------|
| **Primary** | #3B82F6 | White | None | Main actions |
| **Secondary** | White | #3B82F6 | #3B82F6 | Alternative actions |
| **Ghost** | Transparent | #3B82F6 | None | Tertiary actions |
| **Danger** | #EF4444 | White | None | Destructive actions |
| **Disabled** | #E5E7EB | #9CA3AF | None | Cannot click |

**Sizes:**
- Large: height 48px, padding 0 24px
- Medium: height 40px, padding 0 16px
- Small: height 32px, padding 0 12px

**Touch Target:** Minimum 44px height

### Input

| State | Border | Background | Label |
|-------|--------|------------|-------|
| **Default** | #E5E7EB | White | Gray 500 |
| **Focus** | #3B82F6 | White | #3B82F6 |
| **Error** | #EF4444 | White | #EF4444 |
| **Disabled** | #E5E7EB | #F9FAFB | Gray 400 |

**Height:** 44px  
**Padding:** 12px 16px  
**Border Radius:** 8px

### Card

| Property | Value |
|----------|-------|
| Background | White |
| Border | 1px solid #E5E7EB |
| Border Radius | 12px |
| Padding | 16px |
| Shadow | 0 1px 3px rgba(0,0,0,0.1) |

### Badge

| Variant | Background | Text | Usage |
|---------|------------|------|-------|
| **Default** | #F3F4F6 | #374151 | Neutral |
| **Primary** | #DBEAFE | #1D4ED8 | Active |
| **Success** | #D1FAE5 | #065F46 | Completed |
| **Warning** | #FEF3C7 | #92400E | Pending |
| **Danger** | #FEE2E2 | #991B1B | Error |
| **Info** | #DBEAFE | #1D4ED8 | Information |

**Border Radius:** 6px  
**Padding:** 4px 8px  
**Font Size:** 12px

### Toast

| Type | Background | Icon Color | Usage |
|------|------------|------------|-------|
| **Success** | #10B981 | White | Success messages |
| **Error** | #EF4444 | White | Error messages |
| **Warning** | #F59E0B | White | Warning messages |
| **Info** | #3B82F6 | White | Information |

**Position:** Top center  
**Duration:** 3-5 seconds  
**Border Radius:** 8px

### Modal / Bottom Sheet

| Property | Mobile | Desktop |
|----------|--------|---------|
| Position | Bottom | Center |
| Width | 100% | 480px |
| Max Height | 85vh | 80vh |
| Border Radius | 16px 16px 0 0 | 12px |
| Background | White | White |

---

## 5. STATUS BADGES

### Task Status

| Status | Badge Color | Text Color | Label |
|--------|-------------|------------|-------|
| **OPEN** | #DBEAFE | #1D4ED8 | Open |
| **ACCEPTED** | #FEF3C7 | #92400E | Accepted |
| **IN_PROGRESS** | #D1FAE5 | #065F46 | In Progress |
| **WAITING_APPROVAL** | #FEF3C7 | #92400E | Waiting Approval |
| **COMPLETED** | #D1FAE5 | #065F46 | Completed |
| **CANCELLED** | #FEE2E2 | #991B1B | Cancelled |

### Dispute Status

| Status | Badge Color | Text Color | Label |
|--------|-------------|------------|-------|
| **OPEN** | #FEE2E2 | #991B1B | Open |
| **UNDER_REVIEW** | #FEF3C7 | #92400E | Under Review |
| **RESOLVED** | #D1FAE5 | #065F46 | Resolved |
| **REJECTED** | #F3F4F6 | #374151 | Rejected |

### Withdraw Status

| Status | Badge Color | Text Color | Label |
|--------|-------------|------------|-------|
| **PENDING** | #FEF3C7 | #92400E | Pending |
| **APPROVED** | #D1FAE5 | #065F46 | Approved |
| **REJECTED** | #FEE2E2 | #991B1B | Rejected |

---

## 6. NAVIGATION DESIGN

### User Bottom Navigation (5 items)

```
┌─────────────────────────────────────────────────────────────┐
│  ┌─────┐  ┌─────┐  ┌─────┐  ┌─────┐  ┌─────┐             │
│  │ 🏠  │  │ 📋  │  │ ➕  │  │ 🔔  │  │ 👤  │             │
│  │Home │  │Tasks│  │Create│  │Notif│  │Me   │             │
│  └─────┘  └─────┘  └─────┘  └─────┘  └─────┘             │
└─────────────────────────────────────────────────────────────┘
```

| Position | Icon | Label | Route |
|----------|------|-------|-------|
| 1 | Home | Home | /user/dashboard |
| 2 | List | Tasks | /user/tasks |
| 3 | Plus Circle | Create | /user/tasks/create |
| 4 | Bell | Notif | /user/notifications |
| 5 | User | Me | /user/profile |

### Helper Bottom Navigation (5 items)

```
┌─────────────────────────────────────────────────────────────┐
│  ┌─────┐  ┌─────┐  ┌─────┐  ┌─────┐  ┌─────┐             │
│  │ 🏠  │  │ 📋  │  │ 🔍  │  │ 🔔  │  │ 👤  │             │
│  │Home │  │Tasks│  │Find │  │Notif│  │Me   │             │
│  └─────┘  └─────┘  └─────┘  └─────┘  └─────┘             │
└─────────────────────────────────────────────────────────────┘
```

| Position | Icon | Label | Route |
|----------|------|-------|-------|
| 1 | Home | Home | /helper/dashboard |
| 2 | Clipboard | Tasks | /helper/tasks |
| 3 | Search | Find | /helper/available-tasks |
| 4 | Bell | Notif | /helper/notifications |
| 5 | User | Me | /helper/profile |

### Admin Sidebar Navigation

```
┌──────────┬────────────────────────────────────────────────┐
│          │                                                │
│  LOGO    │  Header                            [Avatar]    │
│          │                                                │
├──────────┼────────────────────────────────────────────────┤
│          │                                                │
│ Dashboard│  ┌──────────────────────────────────────────┐  │
│ ─────── │  │                                          │  │
│ Users    │  │           Main Content Area              │  │
│ Helpers  │  │                                          │  │
│ Tasks    │  │                                          │  │
│ Disputes │  │                                          │  │
│ Transact │  │                                          │  │
│ Wallets  │  │                                          │  │
│ Reviews  │  │                                          │  │
│ Categories│  │                                          │  │
│ ─────── │  │                                          │  │
│ Settings │  │                                          │  │
│          │  └──────────────────────────────────────────┘  │
└──────────┴────────────────────────────────────────────────┘
```

| Icon | Label | Route |
|------|-------|-------|
| LayoutDashboard | Dashboard | /admin/dashboard |
| Users | Users | /admin/users |
| UserCheck | Helpers | /admin/helpers |
| ClipboardList | Tasks | /admin/tasks |
| AlertTriangle | Disputes | /admin/disputes |
| CreditCard | Transactions | /admin/transactions |
| Wallet | Wallets | /admin/wallets |
| Star | Reviews | /admin/reviews |
| Tag | Categories | /admin/categories |
| Settings | Settings | /admin/settings |

---

## 7. USER FLOW

### User Task Lifecycle Flow

```
┌─────────────┐
│   Register  │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│    Login    │
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│     Dashboard   │
│  - Saldo        │
│  - Stats        │
│  - Active Tasks │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│   Create Task   │
│  - Title        │
│  - Description  │
│  - Price        │
│  - Deadline     │
│  - Location     │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│   Task Detail   │
│  Status: OPEN   │
└──────┬──────────┘
       │
       │ (Wait for helper)
       ▼
┌─────────────────┐
│   Task Detail   │
│ Status: ACCEPTED│
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│   Task Detail   │
│Status: IN_PROG  │
│  - Progress     │
│  - Attachments  │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│   Task Detail   │
│Status: WAITING  │
│  Approve?       │
│ [Approve][Issue]│
└──────┬──────────┘
       │
       ├──── Approve ────┐
       │                 │
       ▼                 ▼
┌─────────────┐   ┌─────────────┐
│  Complete   │   │   Dispute   │
└──────┬──────┘   └─────────────┘
       │
       ▼
┌─────────────────┐
│  Release Payment│
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│  Leave Review   │
│  ⭐⭐⭐⭐⭐      │
└─────────────────┘
```

### Helper Task Lifecycle Flow

```
┌─────────────┐
│    Login    │
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│     Dashboard   │
│  - Earnings     │
│  - Current Task │
│  - Stats        │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│ Available Tasks │
│  [Search]       │
│  [Filter]       │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│   Task Detail   │
│  [Accept Task]  │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│  My Tasks       │
│ Status: ACCEPTED│
│  [Start Task]   │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│   Task Detail   │
│Status: IN_PROG  │
│  [Add Progress] │
│  [Upload File]  │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│   Task Detail   │
│  [Submit Work]  │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│   Task Detail   │
│Status: WAITING  │
└──────┬──────────┘
       │
       │ (Wait for user)
       ▼
┌─────────────────┐
│   Task Detail   │
│Status: COMPLETED│
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│     Wallet      │
│  Payment Received│
│  [Withdraw]     │
└─────────────────┘
```

### Admin Flow

```
┌─────────────┐
│    Login    │
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│    Dashboard    │
│  - Stats        │
│  - Charts       │
│  - Recent       │
└──────┬──────────┘
       │
       ├───────────────┬───────────────┬───────────────┐
       ▼               ▼               ▼               ▼
┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
│   Users     │ │   Helpers   │ │   Tasks     │ │  Disputes   │
│  - List     │ │  - Verify   │ │  - List     │ │  - Review   │
│  - Detail   │ │  - Reject   │ │  - Detail   │ │  - Resolve  │
│  - Status   │ │  - Detail   │ │  - Monitor  │ │  - Reject   │
└─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘
       │
       ▼
┌─────────────────┐
│  Transactions   │
│  - List         │
│  - Detail       │
│  - Approve/Reject│
│    Withdrawals   │
└─────────────────┘
```

---

## 8. WIREFRAMES

### 8.1 Login Page (Mobile)

```
┌─────────────────────────────────────────┐
│                                         │
│                                         │
│           ┌─────────────────┐           │
│           │     Bantuin     │           │
│           │      Yuk        │           │
│           └─────────────────┘           │
│                                         │
│           ┌─────────────────┐           │
│           │  Email          │           │
│           └─────────────────┘           │
│                                         │
│           ┌─────────────────┐           │
│           │  Password       │           │
│           └─────────────────┘           │
│                                         │
│           ┌─────────────────┐           │
│           │     Login       │           │
│           └─────────────────┘           │
│                                         │
│         Don't have account?             │
│            Register here                │
│                                         │
└─────────────────────────────────────────┘
```

### 8.2 Register Page (Mobile)

```
┌─────────────────────────────────────────┐
│                                         │
│           ┌─────────────────┐           │
│           │     Bantuin     │           │
│           │      Yuk        │           │
│           └─────────────────┘           │
│                                         │
│           ┌─────────────────┐           │
│           │  Name           │           │
│           └─────────────────┘           │
│                                         │
│           ┌─────────────────┐           │
│           │  Email          │           │
│           └─────────────────┘           │
│                                         │
│           ┌─────────────────┐           │
│           │  Phone          │           │
│           └─────────────────┘           │
│                                         │
│           ┌─────────────────┐           │
│           │  Password       │           │
│           └─────────────────┘           │
│                                         │
│           ┌─────────────────┐           │
│           │  Confirm Pass   │           │
│           └─────────────────┘           │
│                                         │
│           ┌─────────────────┐           │
│           │    Register     │           │
│           └─────────────────┘           │
│                                         │
│         Already have account?           │
│            Login here                   │
│                                         │
└─────────────────────────────────────────┘
```

### 8.3 User Dashboard (Mobile)

```
┌─────────────────────────────────────────┐
│ BantuinYuk                    🔔  👤    │
├─────────────────────────────────────────┤
│                                         │
│  ┌─────────────────────────────────┐   │
│  │  💰 Your Balance                │   │
│  │  Rp 500.000                     │   │
│  │  Available: Rp 350.000          │   │
│  └─────────────────────────────────┘   │
│                                         │
│  ┌───────────┐ ┌───────────┐           │
│  │ 📋        │ │ ⏳        │           │
│  │ 5 Tasks   │ │ 2 Active  │           │
│  └───────────┘ └───────────┘           │
│                                         │
│  Quick Actions                          │
│  ┌─────────────────────────────────┐   │
│  │  ➕ Create New Task             │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Active Tasks                           │
│  ┌─────────────────────────────────┐   │
│  │ Renovasi Kamar Mandi     [OPEN] │   │
│  │ Rp 500.000 • 25 Jun            │   │
│  └─────────────────────────────────┘   │
│  ┌─────────────────────────────────┐   │
│  │ Pindahan Rumah      [IN_PROG]  │   │
│  │ Rp 750.000 • 28 Jun            │   │
│  └─────────────────────────────────┘   │
│                                         │
├─────────────────────────────────────────┤
│  🏠    📋    ➕    🔔    👤            │
│ Home  Tasks Create Notif  Me           │
└─────────────────────────────────────────┘
```

### 8.4 Create Task Page (Mobile)

```
┌─────────────────────────────────────────┐
│ ← Create Task                     ✕    │
├─────────────────────────────────────────┤
│                                         │
│  Title                                  │
│  ┌─────────────────────────────────┐   │
│  │ Butuh tukang bangunan           │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Description                            │
│  ┌─────────────────────────────────┐   │
│  │ Butuh tukang untuk renovasi     │   │
│  │ kamar mandi...                  │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Category                               │
│  ┌─────────────────────────────────┐   │
│  │ Pilih kategori...          ▼   │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Price (Rp)                             │
│  ┌─────────────────────────────────┐   │
│  │ 500000                         │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Deadline                               │
│  ┌─────────────────┐ ┌─────────────┐   │
│  │ Start: 20 Jun   │ │ End: 25 Jun │   │
│  └─────────────────┘ └─────────────┘   │
│                                         │
│  Location                               │
│  ┌─────────────────────────────────┐   │
│  │ Jl. Sudirman No. 123           │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Attachments                            │
│  ┌─────────────────────────────────┐   │
│  │  📎 Tap to upload files        │   │
│  │     JPG, PNG, PDF (max 10MB)   │   │
│  └─────────────────────────────────┘   │
│                                         │
│  ┌─────────────────────────────────┐   │
│  │         Create Task             │   │
│  └─────────────────────────────────┘   │
│                                         │
└─────────────────────────────────────────┘
```

### 8.5 Task Detail Page (Mobile)

```
┌─────────────────────────────────────────┐
│ ← Task Detail                           │
├─────────────────────────────────────────┤
│                                         │
│  ┌─────────────────────────────────┐   │
│  │ Renovasi Kamar Mandi    [OPEN]  │   │
│  │ By: John Doe                    │   │
│  └─────────────────────────────────┘   │
│                                         │
│  ┌─────────────────────────────────┐   │
│  │ 💰 Rp 500.000                  │   │
│  │ 📅 20 Jun - 25 Jun 2026        │   │
│  │ 📍 Jl. Sudirman No. 123        │   │
│  │ 📂 Bangunan                     │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Description                            │
│  ┌─────────────────────────────────┐   │
│  │ Butuh tukang bangunan untuk     │   │
│  │ renovasi kamar mandi...         │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Attachments (2)                        │
│  ┌───────────────┐ ┌───────────────┐   │
│  │ 📷 photo1.jpg │ │ 📷 photo2.jpg │   │
│  └───────────────┘ └───────────────┘   │
│                                         │
│  Status History                         │
│  ┌─────────────────────────────────┐   │
│  │ ● Open           13 Jun 12:00  │   │
│  │ │                                │   │
│  │ ○ Waiting Helper                │   │
│  └─────────────────────────────────┘   │
│                                         │
│  ┌─────────────────────────────────┐   │
│  │         Cancel Task             │   │
│  └─────────────────────────────────┘   │
│                                         │
└─────────────────────────────────────────┘
```

### 8.6 Helper Dashboard (Mobile)

```
┌─────────────────────────────────────────┐
│ BantuinYuk                    🔔  👤    │
├─────────────────────────────────────────┤
│                                         │
│  Welcome back, Ahmad! 👋                │
│                                         │
│  ┌─────────────────────────────────┐   │
│  │  💰 This Month                  │   │
│  │  Rp 2.500.000 earned           │   │
│  │  12 tasks completed            │   │
│  └─────────────────────────────────┘   │
│                                         │
│  ┌───────────┐ ┌───────────┐           │
│  │ ⏳        │ │ 📋        │           │
│  │ 1 Active  │ │ 5 Open    │           │
│  └───────────┘ └───────────┘           │
│                                         │
│  Current Task                           │
│  ┌─────────────────────────────────┐   │
│  │ Renovasi Kamar Mandi  [IN_PROG] │   │
│  │ John Doe • Rp 500.000          │   │
│  │ [View Details]                  │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Available Tasks Near You               │
│  ┌─────────────────────────────────┐   │
│  │ Pindahan Rumah          [OPEN]  │   │
│  │ 0.5 km • Rp 750.000            │   │
│  │ [Accept]                        │   │
│  └─────────────────────────────────┘   │
│  ┌─────────────────────────────────┐   │
│  │ Servis AC               [OPEN]  │   │
│  │ 1.2 km • Rp 200.000            │   │
│  │ [Accept]                        │   │
│  └─────────────────────────────────┘   │
│                                         │
├─────────────────────────────────────────┤
│  🏠    📋    🔍    🔔    👤            │
│ Home  Tasks Find  Notif  Me            │
└─────────────────────────────────────────┘
```

### 8.7 Wallet Page (Mobile)

```
┌─────────────────────────────────────────┐
│ ← Wallet                                │
├─────────────────────────────────────────┤
│                                         │
│  ┌─────────────────────────────────┐   │
│  │       Total Balance             │   │
│  │       Rp 1.250.000             │   │
│  │                                 │   │
│  │  Available    Pending           │   │
│  │  Rp 1.000.000  Rp 250.000     │   │
│  └─────────────────────────────────┘   │
│                                         │
│  ┌─────────────────────────────────┐   │
│  │         Withdraw                │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Transaction History                    │
│                                         │
│  Today                                  │
│  ┌─────────────────────────────────┐   │
│  │ 💵 Payment Received      +500K  │   │
│  │    Renovasi Kamar Mandi         │   │
│  │    12:30                        │   │
│  └─────────────────────────────────┘   │
│  ┌─────────────────────────────────┐   │
│  │ 💸 Withdrawal           -200K   │   │
│  │    Bank Transfer                │   │
│  │    10:15                        │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Yesterday                              │
│  ┌─────────────────────────────────┐   │
│  │ 💵 Payment Received      +750K  │   │
│  │    Pindahan Rumah               │   │
│  │    14:00                        │   │
│  └─────────────────────────────────┘   │
│                                         │
└─────────────────────────────────────────┘
```

### 8.8 Notification Page (Mobile)

```
┌─────────────────────────────────────────┐
│ ← Notifications                   Mark   │
│                                   all    │
│                                   read   │
├─────────────────────────────────────────┤
│                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐              │
│  │ All │ │Unread│ │ Read│              │
│  └─────┘ └─────┘ └─────┘              │
│                                         │
│  Unread                                 │
│  ┌─────────────────────────────────┐   │
│  │ 🔵 Task Accepted                │   │
│  │ Ahmad has accepted your task    │   │
│  │ "Renovasi Kamar Mandi"         │   │
│  │ 5 minutes ago                   │   │
│  └─────────────────────────────────┘   │
│  ┌─────────────────────────────────┐   │
│  │ 🔵 New Review                   │   │
│  │ You received a 5-star review   │   │
│  │ from John Doe                   │   │
│  │ 1 hour ago                      │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Earlier                                │
│  ┌─────────────────────────────────┐   │
│  │ Payment Released                │   │
│  │ Rp 500.000 has been released   │   │
│  │ 2 days ago                      │   │
│  └─────────────────────────────────┘   │
│                                         │
├─────────────────────────────────────────┤
│  🏠    📋    ➕    🔔    👤            │
│ Home  Tasks Create Notif  Me           │
└─────────────────────────────────────────┘
```

### 8.9 Dispute Page (Mobile)

```
┌─────────────────────────────────────────┐
│ ← Dispute Detail                        │
├─────────────────────────────────────────┤
│                                         │
│  ┌─────────────────────────────────┐   │
│  │ Work Quality Issue    [OPEN]    │   │
│  │ Task: Renovasi Kamar Mandi      │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Details                                │
│  ┌─────────────────────────────────┐   │
│  │ Created by: John Doe            │   │
│  │ Helper: Ahmad                   │   │
│  │ Date: 14 Jun 2026              │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Reason                                 │
│  ┌─────────────────────────────────┐   │
│  │ Work quality does not meet      │   │
│  │ agreed standards...             │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Timeline                               │
│  ┌─────────────────────────────────┐   │
│  │ ● Dispute Created    14 Jun    │   │
│  │ │                                │   │
│  │ ○ Under Review                  │   │
│  │ │                                │   │
│  │ ○ Resolved                      │   │
│  └─────────────────────────────────┘   │
│                                         │
│  Resolution (if resolved)               │
│  ┌─────────────────────────────────┐   │
│  │ Admin has resolved this dispute │   │
│  │ "Helper must redo the work"     │   │
│  └─────────────────────────────────┘   │
│                                         │
└─────────────────────────────────────────┘
```

### 8.10 Admin Dashboard (Desktop)

```
┌──────────┬────────────────────────────────────────────────┐
│          │  BantuinYuk Admin                  🔔  Admin ▼ │
│  LOGO    ├────────────────────────────────────────────────┤
│          │                                                │
├──────────┤  Dashboard                                     │
│          │                                                │
│ 📊 Dash  │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐ │
│          │  │ 👥 Users │ │ 🔧Tasks │ │ 💰Trans │ │ ⚠️Disp  │ │
│ 👥 Users │  │   150   │ │    45   │ │   230   │ │    3    │ │
│          │  └─────────┘ └─────────┘ └─────────┘ └─────────┘ │
│ 🔧 Helpers│                                               │
│          │  ┌─────────────────────┐ ┌─────────────────────┐ │
│ 📋 Tasks │  │ Recent Tasks        │ │ Pending Withdrawals │ │
│          │  │                     │ │                     │ │
│ ⚠️ Disputes│ │ Task 1  [OPEN]     │ │ Ahmad   Rp 200K    │ │
│          │  │ Task 2  [IN_PROG]  │ │ Siti    Rp 150K    │ │
│ 💳 Trans │  │ Task 3  [WAITING]  │ │ Budi    Rp 300K    │ │
│          │  │                     │ │                     │ │
│ 💼 Wallet│  │ View All →          │ │ View All →          │ │
│          │  └─────────────────────┘ └─────────────────────┘ │
│ ⭐ Reviews│                                                │
│          │  ┌─────────────────────────────────────────────┐ │
│ 🏷️ Categories│ │ Helper Verification Queue                 │ │
│          │  │                                             │ │
│ ⚙️ Settings│ │ Ahmad Smith    KTP: ***1234  [Approve][Reject]│ │
│          │  │ Siti Aminah    KTP: ***5678  [Approve][Reject]│ │
│          │  │                                             │ │
│          │  └─────────────────────────────────────────────┘ │
│          │                                                │
└──────────┴────────────────────────────────────────────────┘
```

---

## 9. EMPTY STATES

### No Tasks

```
┌─────────────────────────────────────────┐
│                                         │
│                                         │
│              📋                         │
│                                         │
│        No tasks yet                     │
│                                         │
│   Create your first task to             │
│   get started                           │
│                                         │
│      ┌─────────────────┐               │
│      │  Create Task    │               │
│      └─────────────────┘               │
│                                         │
│                                         │
└─────────────────────────────────────────┘
```

### No Notifications

```
┌─────────────────────────────────────────┐
│                                         │
│                                         │
│              🔔                         │
│                                         │
│     No notifications                    │
│                                         │
│   You're all caught up!                 │
│                                         │
│                                         │
│                                         │
│                                         │
└─────────────────────────────────────────┘
```

### No Transactions

```
┌─────────────────────────────────────────┐
│                                         │
│                                         │
│              💰                         │
│                                         │
│      No transactions                    │
│                                         │
│   Your transaction history              │
│   will appear here                      │
│                                         │
│                                         │
│                                         │
│                                         │
└─────────────────────────────────────────┘
```

### No Disputes

```
┌─────────────────────────────────────────┐
│                                         │
│                                         │
│              ⚖️                         │
│                                         │
│       No disputes                       │
│                                         │
│   Good news! You have                   │
│   no active disputes                    │
│                                         │
│                                         │
│                                         │
│                                         │
└─────────────────────────────────────────┘
```

---

## 10. LOADING STATES

### Skeleton Card

```
┌─────────────────────────────────────────┐
│  ┌─────────────────────────────────┐   │
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓      │   │
│  │                                 │   │
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  │   │
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓           │   │
│  │                                 │   │
│  │ ▓▓▓▓▓▓  ▓▓▓▓▓▓  ▓▓▓▓▓▓       │   │
│  └─────────────────────────────────┘   │
│                                         │
│  ┌─────────────────────────────────┐   │
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓      │   │
│  │                                 │   │
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  │   │
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓           │   │
│  │                                 │   │
│  │ ▓▓▓▓▓▓  ▓▓▓▓▓▓  ▓▓▓▓▓▓       │   │
│  └─────────────────────────────────┘   │
└─────────────────────────────────────────┘
```

### Skeleton List

```
┌─────────────────────────────────────────┐
│  ┌─────────────────────────────────┐   │
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  │   │
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓               │   │
│  └─────────────────────────────────┘   │
│  ┌─────────────────────────────────┐   │
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  │   │
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓               │   │
│  └─────────────────────────────────┘   │
│  ┌─────────────────────────────────┐   │
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  │   │
│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓               │   │
│  └─────────────────────────────────┘   │
└─────────────────────────────────────────┘
```

### Skeleton Table (Desktop)

```
┌─────────────────────────────────────────────────────────┐
│  ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  │
├─────────────────────────────────────────────────────────┤
│  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓▓▓▓▓    │
│  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓▓▓▓▓    │
│  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓▓▓▓▓    │
│  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓▓▓▓▓▓    │
└─────────────────────────────────────────────────────────┘
```

---

## 11. ERROR STATES

### 404 Not Found

```
┌─────────────────────────────────────────┐
│                                         │
│                                         │
│              🔍                         │
│                                         │
│          404                            │
│     Page not found                      │
│                                         │
│   The page you're looking for           │
│   doesn't exist or has been moved       │
│                                         │
│      ┌─────────────────┐               │
│      │   Go Home       │               │
│      └─────────────────┘               │
│                                         │
│                                         │
└─────────────────────────────────────────┘
```

### 500 Server Error

```
┌─────────────────────────────────────────┐
│                                         │
│                                         │
│              ⚠️                         │
│                                         │
│        Something went wrong             │
│                                         │
│   We're having trouble connecting       │
│   to our servers. Please try again      │
│                                         │
│      ┌─────────────────┐               │
│      │     Retry       │               │
│      └─────────────────┘               │
│                                         │
│                                         │
└─────────────────────────────────────────┘
```

### Network Error

```
┌─────────────────────────────────────────┐
│                                         │
│                                         │
│              📡                         │
│                                         │
│      No internet connection             │
│                                         │
│   Please check your network             │
│   settings and try again                │
│                                         │
│      ┌─────────────────┐               │
│      │     Retry       │               │
│      └─────────────────┘               │
│                                         │
│                                         │
└─────────────────────────────────────────┘
```

### Unauthorized

```
┌─────────────────────────────────────────┐
│                                         │
│                                         │
│              🔒                         │
│                                         │
│     Session expired                    │
│                                         │
│   Please login again to                 │
│   continue                             │
│                                         │
│      ┌─────────────────┐               │
│      │     Login       │               │
│      └─────────────────┘               │
│                                         │
│                                         │
└─────────────────────────────────────────┘
```

---

## 12. RESPONSIVE STRATEGY

### Breakpoints

| Breakpoint | Width | Target | Layout Changes |
|------------|-------|--------|----------------|
| `sm` | 640px | Large Mobile | Single column, bottom nav |
| `md` | 768px | Tablet | Two columns, sidebar hidden |
| `lg` | 1024px | Desktop | Full layout, sidebar visible (admin) |
| `xl` | 1280px | Wide Desktop | Max-width container |

### Mobile (< 640px)

```
┌─────────────────┐
│     Header      │
├─────────────────┤
│                 │
│  Single Column  │
│     Content     │
│                 │
├─────────────────┤
│  Bottom Nav     │
└─────────────────┘
```

- Full width layout
- Bottom navigation visible
- Cards stack vertically
- Modals become bottom sheets
- Touch-optimized (44px targets)

### Tablet (640px - 1024px)

```
┌─────────────────┐
│     Header      │
├─────────────────┤
│  │              │
│  │  Two Column  │
│  │  Content     │
│  │              │
├─────────────────┤
│  Bottom Nav     │
└─────────────────┘
```

- Two-column grid for cards
- Bottom navigation still visible
- Slightly larger touch targets
- More content visible per screen

### Desktop (> 1024px) - Admin Only

```
┌──────────┬────────────────────┐
│ Sidebar  │      Header        │
│          ├────────────────────┤
│          │                    │
│          │    Main Content    │
│          │                    │
│          │                    │
└──────────┴────────────────────┘
```

- Sidebar navigation visible
- Three-column grid for tables
- Hover states enabled
- Keyboard navigation optimized
- Right-click context menus

---

## 13. ACCESSIBILITY

### Minimum Requirements

| Requirement | Implementation |
|-------------|----------------|
| **Touch Target** | 44px × 44px minimum |
| **Contrast Ratio** | 4.5:1 for normal text, 3:1 for large text |
| **Focus Indicators** | Visible focus ring on interactive elements |
| **Alt Text** | All images have descriptive alt text |
| **Semantic HTML** | Proper heading hierarchy, landmarks |
| **Screen Reader** | ARIA labels for icons, live regions for toasts |
| **Keyboard Navigation** | All interactive elements focusable |
| **Color Independence** | Never use color alone to convey information |

### Color Contrast Checklist

| Element | Foreground | Background | Ratio | Status |
|---------|------------|------------|-------|--------|
| Body Text | #374151 | #FFFFFF | 10.4:1 | ✅ |
| Secondary Text | #6B7280 | #FFFFFF | 5.0:1 | ✅ |
| Primary Button | #FFFFFF | #3B82F6 | 4.6:1 | ✅ |
| Error Text | #EF4444 | #FFFFFF | 4.5:1 | ✅ |
| Success Badge | #065F46 | #D1FAE5 | 4.7:1 | ✅ |

---

## 14. UI DEVELOPMENT PRIORITY

### Phase 1: Authentication (Sprint 13.2)

| Priority | Component | Complexity |
|----------|-----------|------------|
| 1 | Login Page | Low |
| 2 | Register Page | Low |
| 3 | Auth Store | Medium |
| 4 | Auth Guards | Medium |

### Phase 2: Layout (Sprint 13.1)

| Priority | Component | Complexity |
|----------|-----------|------------|
| 1 | User Layout + Bottom Nav | Medium |
| 2 | Helper Layout + Bottom Nav | Medium |
| 3 | Admin Layout + Sidebar | Medium |
| 4 | Header Component | Low |
| 5 | Notification Badge | Low |

### Phase 3: Task Module (Sprint 13.3)

| Priority | Component | Complexity |
|----------|-----------|------------|
| 1 | Task Card | Low |
| 2 | Task List | Medium |
| 3 | Task Detail | High |
| 4 | Create Task Form | High |
| 5 | Task Filters | Medium |
| 6 | Status Badge | Low |

### Phase 4: Helper Module (Sprint 13.4)

| Priority | Component | Complexity |
|----------|-----------|------------|
| 1 | Available Tasks | Medium |
| 2 | Helper Profile | Medium |
| 3 | Accept/Start/Submit Actions | High |
| 4 | Progress List | Medium |
| 5 | File Upload | Medium |
| 6 | Helper Stats | Low |

### Phase 5: Wallet + Notification (Sprint 13.5)

| Priority | Component | Complexity |
|----------|-----------|------------|
| 1 | Wallet Summary | Low |
| 2 | Transaction List | Medium |
| 3 | Withdraw Form | Medium |
| 4 | Notification List | Low |
| 5 | Notification Bell | Low |

### Phase 6: Admin Dashboard (Sprint 13.6)

| Priority | Component | Complexity |
|----------|-----------|------------|
| 1 | Dashboard Stats | Medium |
| 2 | User Management | High |
| 3 | Helper Management | High |
| 4 | Task Management | High |
| 5 | Dispute Management | High |
| 6 | Transaction Management | Medium |

---

**Document Ready for Frontend Implementation**
