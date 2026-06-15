# FRONTEND ARCHITECTURE

**Status:** ✅ FINAL - Blueprint Ready  
**Date:** 2026-06-14  
**Backend:** CodeIgniter 4 REST API (Final)  
**Documentation:** OpenAPI 3.0.3

---

## 1. TECH STACK DECISION

### Final Stack

| Technology | Choice | Reason |
|------------|--------|--------|
| **Framework** | React 18 + Vite | Simpler, lighter, faster for MVP |
| **PWA** | vite-plugin-pwa | Built-in service worker, offline support |
| **State** | Zustand | Minimal boilerplate, TypeScript-first |
| **Forms** | React Hook Form | Performant, minimal re-renders |
| **Data Fetching** | TanStack Query | Caching, refetch, optimistic updates |
| **Styling** | Tailwind CSS + shadcn/ui | Modern, customizable, accessible |
| **Routing** | React Router v6 | Standard, well-documented |
| **HTTP Client** | Axios | Interceptors, cancel tokens |
| **Language** | TypeScript | Type safety, better DX |

### Stack Selection Analysis

#### Framework: React 18 + Vite

| Option | Pros | Cons | Verdict |
|--------|------|------|---------|
| **React + Vite** | Fast build, simple config, huge ecosystem | Manual PWA setup | ✅ Selected |
| Next.js | SSR, SSG, built-in routing | Overkill for API-only backend | ❌ |
| Vue 3 | Easy learning curve | Smaller ecosystem | ❌ |

**Decision Rationale:**
1. BantuinYuk backend is REST API only (no SSR needed)
2. React has larger ecosystem for PWA libraries
3. Vite has faster dev server and build times
4. Simpler mental model for MVP

#### State Management: Zustand

| Option | Pros | Cons | Verdict |
|--------|------|------|---------|
| **Zustand** | Tiny, simple, TypeScript-first | Less devtools | ✅ Selected |
| Redux Toolkit | Powerful devtools | Boilerplate, overkill | ❌ |
| Context API | Built-in | Performance issues | ❌ |

**Decision Rationale:**
1. Minimal boilerplate code
2. Built-in persistence middleware
3. No providers needed
4. Perfect for MVP scope

#### Forms: React Hook Form

| Option | Pros | Cons | Verdict |
|--------|------|------|---------|
| **React Hook Form** | Minimal re-renders, easy validation | Learning curve | ✅ Selected |
| Formik | Popular | Performance issues | ❌ |
| Native State | Simple | Verbose | ❌ |

#### Data Fetching: TanStack Query

| Option | Pros | Cons | Verdict |
|--------|------|------|---------|
| **TanStack Query** | Caching, refetch, mutations | Learning curve | ✅ Selected |
| SWR | Simple | Less features | ❌ |
| Manual Fetch | No dependency | Too much code | ❌ |

#### Styling: Tailwind + shadcn/ui

| Option | Pros | Cons | Verdict |
|--------|------|------|---------|
| **Tailwind + shadcn** | Fast, customizable, accessible | Custom setup | ✅ Selected |
| MUI | Components included | Heavy, opinionated | ❌ |
| Ant Design | Many components | Enterprise feel | ❌ |

---

## 2. FOLDER STRUCTURE

```
src/
├── app/
│   ├── App.tsx                    # Root component
│   ├── main.tsx                   # Entry point
│   └── providers.tsx              # App providers wrapper
│
├── assets/
│   ├── icons/                     # SVG icons
│   ├── images/                    # Static images
│   └── logo.svg                   # App logo
│
├── components/
│   ├── ui/                        # shadcn/ui components
│   │   ├── button.tsx
│   │   ├── card.tsx
│   │   ├── dialog.tsx
│   │   ├── dropdown-menu.tsx
│   │   ├── form.tsx
│   │   ├── input.tsx
│   │   ├── select.tsx
│   │   ├── table.tsx
│   │   ├── toast.tsx
│   │   └── ...
│   │
│   ├── layout/                    # Layout components
│   │   ├── AuthLayout.tsx         # Guest layout (login/register)
│   │   ├── UserLayout.tsx         # User dashboard layout
│   │   ├── HelperLayout.tsx       # Helper dashboard layout
│   │   ├── AdminLayout.tsx        # Admin dashboard layout
│   │   ├── Header.tsx             # Top navigation
│   │   ├── Sidebar.tsx            # Admin sidebar
│   │   ├── BottomNav.tsx          # Mobile bottom nav
│   │   └── NotificationBadge.tsx  # Notification indicator
│   │
│   └── shared/                    # Shared components
│       ├── LoadingSpinner.tsx
│       ├── ErrorMessage.tsx
│       ├── EmptyState.tsx
│       ├── ConfirmDialog.tsx
│       ├── FileUpload.tsx
│       ├── StatusBadge.tsx
│       ├── Avatar.tsx
│       └── Pagination.tsx
│
├── features/
│   ├── auth/
│   │   ├── components/
│   │   │   ├── LoginForm.tsx
│   │   │   ├── RegisterForm.tsx
│   │   │   └── ProfileCard.tsx
│   │   ├── hooks/
│   │   │   ├── useAuth.ts
│   │   │   └── useLogin.ts
│   │   ├── services/
│   │   │   └── auth.service.ts
│   │   ├── stores/
│   │   │   └── auth.store.ts
│   │   └── types/
│   │       └── auth.types.ts
│   │
│   ├── tasks/
│   │   ├── components/
│   │   │   ├── TaskCard.tsx
│   │   │   ├── TaskList.tsx
│   │   │   ├── TaskDetail.tsx
│   │   │   ├── TaskForm.tsx
│   │   │   ├── TaskStatusBadge.tsx
│   │   │   └── TaskFilters.tsx
│   │   ├── hooks/
│   │   │   ├── useTasks.ts
│   │   │   ├── useTask.ts
│   │   │   └── useCreateTask.ts
│   │   ├── pages/
│   │   │   ├── TaskListPage.tsx
│   │   │   ├── TaskDetailPage.tsx
│   │   │   ├── CreateTaskPage.tsx
│   │   │   └── MyTasksPage.tsx
│   │   ├── services/
│   │   │   └── task.service.ts
│   │   └── types/
│   │       └── task.types.ts
│   │
│   ├── helper/
│   │   ├── components/
│   │   │   ├── HelperCard.tsx
│   │   │   ├── HelperProfile.tsx
│   │   │   ├── AvailableTasks.tsx
│   │   │   └── HelperStats.tsx
│   │   ├── hooks/
│   │   │   ├── useHelperProfile.ts
│   │   │   ├── useAvailableTasks.ts
│   │   │   └── useHelperTasks.ts
│   │   ├── pages/
│   │   │   ├── HelperDashboard.tsx
│   │   │   ├── HelperProfilePage.tsx
│   │   │   └── HelperTasksPage.tsx
│   │   ├── services/
│   │   │   └── helper.service.ts
│   │   └── types/
│   │       └── helper.types.ts
│   │
│   ├── attachment/
│   │   ├── components/
│   │   │   ├── FileUploadZone.tsx
│   │   │   ├── AttachmentList.tsx
│   │   │   └── AttachmentPreview.tsx
│   │   ├── hooks/
│   │   │   └── useAttachments.ts
│   │   ├── services/
│   │   │   └── attachment.service.ts
│   │   └── types/
│   │       └── attachment.types.ts
│   │
│   ├── progress/
│   │   ├── components/
│   │   │   ├── ProgressList.tsx
│   │   │   ├── ProgressForm.tsx
│   │   │   └── ProgressItem.tsx
│   │   ├── hooks/
│   │   │   └── useProgress.ts
│   │   ├── services/
│   │   │   └── progress.service.ts
│   │   └── types/
│   │       └── progress.types.ts
│   │
│   ├── review/
│   │   ├── components/
│   │   │   ├── ReviewForm.tsx
│   │   │   ├── ReviewList.tsx
│   │   │   ├── ReviewCard.tsx
│   │   │   └── RatingStars.tsx
│   │   ├── hooks/
│   │   │   ├── useReviews.ts
│   │   │   └── useRatingSummary.ts
│   │   ├── pages/
│   │   │   └── HelperReviewsPage.tsx
│   │   ├── services/
│   │   │   └── review.service.ts
│   │   └── types/
│   │       └── review.types.ts
│   │
│   ├── wallet/
│   │   ├── components/
│   │   │   ├── WalletSummary.tsx
│   │   │   ├── TransactionList.tsx
│   │   │   ├── TransactionCard.tsx
│   │   │   ├── WithdrawForm.tsx
│   │   │   └── BalanceCard.tsx
│   │   ├── hooks/
│   │   │   ├── useWallet.ts
│   │   │   └── useTransactions.ts
│   │   ├── pages/
│   │   │   ├── WalletPage.tsx
│   │   │   └── TransactionHistoryPage.tsx
│   │   ├── services/
│   │   │   └── wallet.service.ts
│   │   └── types/
│   │       └── wallet.types.ts
│   │
│   ├── notification/
│   │   ├── components/
│   │   │   ├── NotificationList.tsx
│   │   │   ├── NotificationItem.tsx
│   │   │   └── NotificationBell.tsx
│   │   ├── hooks/
│   │   │   ├── useNotifications.ts
│   │   │   └── useUnreadCount.ts
│   │   ├── pages/
│   │   │   └── NotificationsPage.tsx
│   │   ├── services/
│   │   │   └── notification.service.ts
│   │   └── types/
│   │       └── notification.types.ts
│   │
│   ├── dispute/
│   │   ├── components/
│   │   │   ├── DisputeForm.tsx
│   │   │   ├── DisputeCard.tsx
│   │   │   └── DisputeTimeline.tsx
│   │   ├── hooks/
│   │   │   └── useDisputes.ts
│   │   ├── pages/
│   │   │   ├── DisputeListPage.tsx
│   │   │   └── DisputeDetailPage.tsx
│   │   ├── services/
│   │   │   └── dispute.service.ts
│   │   └── types/
│   │       └── dispute.types.ts
│   │
│   └── admin/
│       ├── components/
│       │   ├── DashboardStats.tsx
│       │   ├── UserTable.tsx
│       │   ├── HelperTable.tsx
│       │   ├── TaskTable.tsx
│       │   ├── TransactionTable.tsx
│       │   ├── WalletTable.tsx
│       │   ├── DisputeTable.tsx
│       │   ├── AnalyticsChart.tsx
│       │   └── AdminFilters.tsx
│       ├── hooks/
│       │   ├── useDashboard.ts
│       │   ├── useAdminUsers.ts
│       │   ├── useAdminHelpers.ts
│       │   ├── useAdminTasks.ts
│       │   ├── useAdminTransactions.ts
│       │   ├── useAdminWallets.ts
│       │   └── useAdminDisputes.ts
│       ├── pages/
│       │   ├── DashboardPage.tsx
│       │   ├── UsersPage.tsx
│       │   ├── UserDetailPage.tsx
│       │   ├── HelpersPage.tsx
│       │   ├── HelperDetailPage.tsx
│       │   ├── TasksPage.tsx
│       │   ├── TaskDetailPage.tsx
│       │   ├── TransactionsPage.tsx
│       │   ├── WalletsPage.tsx
│       │   ├── DisputesPage.tsx
│       │   └── AnalyticsPage.tsx
│       ├── services/
│       │   └── admin.service.ts
│       └── types/
│           └── admin.types.ts
│
├── hooks/
│   ├── useDebounce.ts
│   ├── useLocalStorage.ts
│   ├── useMediaQuery.ts
│   ├── useOnlineStatus.ts
│   └── usePagination.ts
│
├── lib/
│   ├── api.ts                     # Axios instance
│   ├── constants.ts               # App constants
│   ├── helpers.ts                  # Utility functions
│   └── validators.ts              # Zod schemas
│
├── routes/
│   ├── index.tsx                  # Route definitions
│   ├── AuthGuard.tsx              # Auth protection
│   ├── RoleGuard.tsx              # Role protection
│   └── GuestGuard.tsx             # Guest protection
│
├── services/
│   ├── api.service.ts             # Base API service
│   └── storage.service.ts         # LocalStorage helpers
│
├── stores/
│   ├── auth.store.ts              # Auth state
│   ├── ui.store.ts                # UI state (sidebar, theme)
│   └── notification.store.ts      # Notification state
│
├── styles/
│   └── globals.css                # Global styles
│
├── types/
│   ├── api.types.ts               # API response types
│   ├── index.ts                   # Shared types
│   └── pagination.types.ts        # Pagination types
│
├── utils/
│   ├── cn.ts                      # className utility
│   ├── format.ts                  # Date/number formatting
│   └── storage.ts                 # Storage helpers
│
├── pwa/
│   ├── manifest.json              # PWA manifest
│   └── sw.js                      # Service worker (auto-generated)
│
├── index.html                     # HTML entry
├── vite.config.ts                 # Vite config
├── tailwind.config.js             # Tailwind config
├── tsconfig.json                  # TypeScript config
├── package.json                   # Dependencies
└── .env.example                   # Environment variables
```

---

## 3. ROUTE MAP

### Public Routes (No Auth)

```
/login              → LoginPage
/register           → RegisterPage
```

### User Routes (Auth Required: user)

```
/user/dashboard     → UserDashboard
/user/tasks         → TaskListPage
/user/tasks/:id     → TaskDetailPage
/user/tasks/create  → CreateTaskPage
/user/history       → TaskHistoryPage
/user/wallet        → WalletPage
/user/transactions  → TransactionHistoryPage
/user/notifications → NotificationsPage
/user/profile       → ProfilePage
/user/disputes      → DisputeListPage
/user/disputes/:id  → DisputeDetailPage
```

### Helper Routes (Auth Required: helper)

```
/helper/dashboard         → HelperDashboard
/helper/tasks             → HelperTasksPage
/helper/tasks/:id         → HelperTaskDetailPage
/helper/available-tasks   → AvailableTasksPage
/helper/profile           → HelperProfilePage
/helper/edit-profile      → EditProfilePage
/helper/location          → LocationPage
/helper/wallet            → WalletPage
/helper/transactions      → TransactionHistoryPage
/helper/notifications     → NotificationsPage
/helper/reviews           → HelperReviewsPage
/helper/disputes          → DisputeListPage
/helper/disputes/:id      → DisputeDetailPage
```

### Admin Routes (Auth Required: admin)

```
/admin/dashboard              → DashboardPage
/admin/analytics              → AnalyticsPage
/admin/users                  → UsersPage
/admin/users/:id              → UserDetailPage
/admin/helpers                → HelpersPage
/admin/helpers/:id            → HelperDetailPage
/admin/tasks                  → TasksPage
/admin/tasks/:id              → TaskDetailPage
/admin/transactions           → TransactionsPage
/admin/transactions/:id       → TransactionDetailPage
/admin/wallets                → WalletsPage
/admin/disputes               → DisputesPage
/admin/disputes/:id           → DisputeDetailPage
/admin/categories             → CategoriesPage
/admin/reviews                → ReviewsPage
/admin/settings               → SettingsPage
```

---

## 4. ROLE-BASED ROUTING

### Guard Types

| Guard | Purpose | Behavior |
|-------|---------|----------|
| **AuthGuard** | Protected routes | Redirect → `/login` if no token |
| **RoleGuard** | Role-specific routes | Redirect → `/{role}/dashboard` if wrong role |
| **GuestGuard** | Public only routes | Redirect → `/{role}/dashboard` if logged in |

### Route Protection Flow

```
┌─────────────────────────────────────────────────────────────┐
│                        App Start                             │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    Check Token in Storage                    │
└─────────────────────────────────────────────────────────────┘
                              │
              ┌───────────────┴───────────────┐
              ▼                               ▼
        Token Exists                    No Token
              │                               │
              ▼                               ▼
┌──────────────────────────┐    ┌──────────────────────────┐
│    Fetch User Profile    │    │     Guest Guard Only      │
│    GET /auth/me          │    │     /login, /register     │
└──────────────────────────┘    └──────────────────────────┘
              │
              ▼
┌──────────────────────────┐
│    Check User Role       │
│    user / helper / admin │
└──────────────────────────┘
              │
    ┌─────────┼─────────┐
    ▼         ▼         ▼
  User     Helper    Admin
    │         │         │
    ▼         ▼         ▼
/user/*   /helper/*  /admin/*
```

### Route Protection Matrix

| Route Pattern | Guard | Allowed Roles |
|---------------|-------|---------------|
| `/login`, `/register` | GuestGuard | None (guest only) |
| `/user/*` | AuthGuard + RoleGuard | user |
| `/helper/*` | AuthGuard + RoleGuard | helper |
| `/admin/*` | AuthGuard + RoleGuard | admin |

---

## 5. STATE MANAGEMENT

### Zustand Store Structure

#### Auth Store
```typescript
interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  
  // Actions
  login: (email: string, password: string) => Promise<void>;
  register: (data: RegisterData) => Promise<void>;
  logout: () => void;
  setUser: (user: User) => void;
  updateProfile: (data: Partial<User>) => void;
}
```

#### UI Store
```typescript
interface UIState {
  sidebarOpen: boolean;
  theme: 'light' | 'dark';
  notifications: Notification[];
  unreadCount: number;
  
  // Actions
  toggleSidebar: () => void;
  setTheme: (theme: 'light' | 'dark') => void;
  setNotifications: (notifications: Notification[]) => void;
  incrementUnreadCount: () => void;
  decrementUnreadCount: () => void;
}
```

#### Store Usage Pattern

```typescript
// In component
const { user, isAuthenticated } = useAuthStore();

// In hooks (recommended pattern)
export const useAuth = () => {
  const store = useAuthStore();
  
  const isUser = store.user?.role === 'user';
  const isHelper = store.user?.role === 'helper';
  const isAdmin = store.user?.role === 'admin';
  
  return {
    ...store,
    isUser,
    isHelper,
    isAdmin,
  };
};
```

---

## 6. API LAYER

### Axios Instance Configuration

```typescript
// lib/api.ts
- Base URL: VITE_API_URL
- Timeout: 30000ms
- Headers: { Content-Type: application/json }
```

### Request Interceptor

```
1. Get token from auth store
2. If token exists, add to header:
   Authorization: Bearer {token}
3. Return config
```

### Response Interceptor

```
1. Check response status
2. If 401 → Logout user, redirect to /login
3. If 422 → Return validation errors
4. If 500 → Show generic error
5. Return response.data
```

### Service Pattern

```typescript
// features/{module}/services/{module}.service.ts

export const moduleService = {
  // List
  getList: (params?: ListParams) =>
    api.get('/endpoint', { params }),
    
  // Get by ID
  getById: (id: number) =>
    api.get(`/endpoint/${id}`),
    
  // Create
  create: (data: CreateData) =>
    api.post('/endpoint', data),
    
  // Update
  update: (id: number, data: UpdateData) =>
    api.put(`/endpoint/${id}`, data),
    
  // Delete
  remove: (id: number) =>
    api.delete(`/endpoint/${id}`),
};
```

### Services Map

| Service | Base URL | Key Methods |
|---------|----------|-------------|
| `auth.service.ts` | `/auth` | login, register, logout, getMe |
| `task.service.ts` | `/tasks` | list, getById, create, update, delete, complete |
| `helper.service.ts` | `/helpers` | list, getById, getProfile, updateProfile, updateLocation |
| `attachment.service.ts` | `/tasks/{id}/attachments` | list, upload, delete |
| `progress.service.ts` | `/tasks/{id}/progress` | list, create |
| `review.service.ts` | `/tasks/{id}/review` | get, create, getHelperReviews, getRatingSummary |
| `wallet.service.ts` | `/wallet` | getSummary, getTransactions, withdraw, releasePayment |
| `notification.service.ts` | `/notifications` | list, unreadCount, markRead, markAllRead |
| `dispute.service.ts` | `/disputes` | list, getById, create |
| `admin.service.ts` | `/admin` | dashboard, analytics, users, helpers, tasks, transactions, wallets, disputes |

---

## 7. DATA FETCHING (TanStack Query)

### Query Keys Pattern

```typescript
export const queryKeys = {
  auth: {
    me: ['auth', 'me'],
  },
  tasks: {
    all: ['tasks'],
    lists: () => [...queryKeys.tasks.all, 'list'],
    list: (params: ListParams) => [...queryKeys.tasks.lists(), params],
    details: () => [...queryKeys.tasks.all, 'detail'],
    detail: (id: number) => [...queryKeys.tasks.details(), id],
    my: (params: ListParams) => [...queryKeys.tasks.all, 'my', params],
  },
  helpers: {
    all: ['helpers'],
    lists: () => [...queryKeys.helpers.all, 'list'],
    list: (params: ListParams) => [...queryKeys.helpers.lists(), params],
    details: () => [...queryKeys.helpers.all, 'detail'],
    detail: (id: number) => [...queryKeys.helpers.details(), id],
    profile: ['helpers', 'profile'],
    availableTasks: ['helpers', 'available-tasks'],
    tasks: (params: ListParams) => [...queryKeys.helpers.all, 'tasks', params],
  },
  wallet: {
    summary: ['wallet', 'summary'],
    transactions: (params: ListParams) => ['wallet', 'transactions', params],
  },
  notifications: {
    list: (params: ListParams) => ['notifications', 'list', params],
    unreadCount: ['notifications', 'unread-count'],
  },
  reviews: {
    helper: (id: number, params: ListParams) => ['reviews', 'helper', id, params],
    ratingSummary: (id: number) => ['reviews', 'rating-summary', id],
  },
  disputes: {
    all: ['disputes'],
    list: (params: ListParams) => [...queryKeys.disputes.all, 'list', params],
    detail: (id: number) => [...queryKeys.disputes.all, 'detail', id],
  },
  admin: {
    dashboard: ['admin', 'dashboard'],
    analytics: ['admin', 'analytics'],
    users: (params: ListParams) => ['admin', 'users', params],
    helpers: (params: ListParams) => ['admin', 'helpers', params],
    tasks: (params: ListParams) => ['admin', 'tasks', params],
    transactions: (params: ListParams) => ['admin', 'transactions', params],
    wallets: (params: ListParams) => ['admin', 'wallets', params],
    disputes: (params: ListParams) => ['admin', 'disputes', params],
  },
};
```

### Query Hooks Pattern

```typescript
// Example: useTasks.ts
export const useTasks = (params: ListParams) => {
  return useQuery({
    queryKey: queryKeys.tasks.list(params),
    queryFn: () => taskService.getList(params),
    staleTime: 30000, // 30 seconds
  });
};

export const useTask = (id: number) => {
  return useQuery({
    queryKey: queryKeys.tasks.detail(id),
    queryFn: () => taskService.getById(id),
    enabled: !!id,
  });
};
```

### Mutation Pattern

```typescript
// Example: useCreateTask.ts
export const useCreateTask = () => {
  const queryClient = useQueryClient();
  const navigate = useNavigate();
  
  return useMutation({
    mutationFn: (data: CreateTaskData) => taskService.create(data),
    onSuccess: (data) => {
      queryClient.invalidateQueries({ queryKey: queryKeys.tasks.lists() });
      navigate(`/user/tasks/${data.id}`);
      toast.success('Task created successfully');
    },
    onError: (error) => {
      toast.error(error.message);
    },
  });
};
```

### Optimistic Update Pattern

```typescript
// Example: Mark notification as read
export const useMarkNotificationRead = () => {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: (id: number) => notificationService.markRead(id),
    onMutate: async (id) => {
      // Cancel outgoing refetches
      await queryClient.cancelQueries({ queryKey: queryKeys.notifications.unreadCount });
      
      // Snapshot previous value
      const previous = queryClient.getQueryData(queryKeys.notifications.unreadCount);
      
      // Optimistically update
      queryClient.setQueryData(queryKeys.notifications.unreadCount, (old: any) => ({
        ...old,
        unread_count: Math.max(0, (old?.unread_count || 1) - 1),
      }));
      
      return { previous };
    },
    onError: (err, variables, context) => {
      queryClient.setQueryData(queryKeys.notifications.unreadCount, context?.previous);
    },
    onSettled: () => {
      queryClient.invalidateQueries({ queryKey: queryKeys.notifications.all });
    },
  });
};
```

---

## 8. MODULE ARCHITECTURE

### 8.1 Auth Module

| Component | Description |
|-----------|-------------|
| **Pages** | LoginPage, RegisterPage |
| **Components** | LoginForm, RegisterForm, ProfileCard |
| **Hooks** | useAuth, useLogin, useRegister |
| **Store** | auth.store.ts |
| **Service** | auth.service.ts |

**Flow:**
```
/login → POST /auth/login → Store token → Redirect to /{role}/dashboard
/register → POST /auth/register → Auto login → Redirect to /user/dashboard
```

### 8.2 Task Module

| Component | Description |
|-----------|-------------|
| **Pages** | TaskListPage, TaskDetailPage, CreateTaskPage, MyTasksPage |
| **Components** | TaskCard, TaskList, TaskDetail, TaskForm, TaskStatusBadge, TaskFilters |
| **Hooks** | useTasks, useTask, useCreateTask, useUpdateTask |
| **Service** | task.service.ts |

**Flows:**
```
User: /user/tasks/create → POST /tasks → Redirect to task detail
User: /user/tasks/:id → GET /tasks/:id → Show task with status history
Helper: /helper/available-tasks → GET /tasks?status=open → Accept task
```

### 8.3 Helper Module

| Component | Description |
|-----------|-------------|
| **Pages** | HelperDashboard, HelperProfilePage, HelperTasksPage |
| **Components** | HelperCard, HelperProfile, AvailableTasks, HelperStats |
| **Hooks** | useHelperProfile, useAvailableTasks, useHelperTasks |
| **Service** | helper.service.ts |

**Flows:**
```
Helper: /helper/profile → GET /helpers/profile → Show/edit profile
Helper: /helper/tasks/:id/accept → POST /helpers/tasks/:id/accept → Update task status
Helper: /helper/tasks/:id/start → POST /helpers/tasks/:id/start → Begin work
Helper: /helper/tasks/:id/submit → POST /helpers/tasks/:id/submit → Submit work
```

### 8.4 Attachment Module

| Component | Description |
|-----------|-------------|
| **Components** | FileUploadZone, AttachmentList, AttachmentPreview |
| **Hooks** | useAttachments |
| **Service** | attachment.service.ts |

**Upload Flow:**
```
1. User selects file
2. Validate: type (jpg/png/webp/pdf), size (<10MB)
3. POST /tasks/{id}/attachments (multipart/form-data)
4. Server returns TaskAttachment
5. Update attachment list
```

### 8.5 Progress Module

| Component | Description |
|-----------|-------------|
| **Components** | ProgressList, ProgressForm, ProgressItem |
| **Hooks** | useProgress |
| **Service** | progress.service.ts |

**Flow:**
```
Helper: /helper/tasks/:id → POST /tasks/{id}/progress → Auto status to in_progress
```

### 8.6 Review Module

| Component | Description |
|-----------|-------------|
| **Components** | ReviewForm, ReviewList, ReviewCard, RatingStars |
| **Hooks** | useReviews, useRatingSummary |
| **Pages** | HelperReviewsPage |
| **Service** | review.service.ts |

**Flow:**
```
User: /user/tasks/:id → POST /tasks/{id}/review → Update helper rating
```

### 8.7 Wallet Module

| Component | Description |
|-----------|-------------|
| **Pages** | WalletPage, TransactionHistoryPage |
| **Components** | WalletSummary, TransactionList, TransactionCard, WithdrawForm, BalanceCard |
| **Hooks** | useWallet, useTransactions |
| **Service** | wallet.service.ts |

**Flows:**
```
User: /user/wallet → GET /wallet → Show balance
Helper: /helper/wallet → GET /wallet → Show balance + withdraw option
Helper: Withdraw → POST /wallet/withdraw → Pending approval
User: Release payment → POST /wallet/release-payment/{taskId} → Payment to helper
```

### 8.8 Notification Module

| Component | Description |
|-----------|-------------|
| **Pages** | NotificationsPage |
| **Components** | NotificationList, NotificationItem, NotificationBell |
| **Hooks** | useNotifications, useUnreadCount |
| **Service** | notification.service.ts |

**Flow:**
```
Header: NotificationBell → GET /notifications/unread-count → Show badge
Page: /notifications → GET /notifications → List all
Click: Mark as read → POST /notifications/{id}/read → Update badge
```

### 8.9 Dispute Module

| Component | Description |
|-----------|-------------|
| **Pages** | DisputeListPage, DisputeDetailPage |
| **Components** | DisputeForm, DisputeCard, DisputeTimeline |
| **Hooks** | useDisputes |
| **Service** | dispute.service.ts |

**Flow:**
```
User/Helper: Create dispute → POST /disputes → Status: open
Admin: Review → POST /admin/disputes/{id}/review → Status: under_review
Admin: Resolve → POST /admin/disputes/{id}/resolve → Status: resolved
```

### 8.10 Admin Module

| Component | Description |
|-----------|-------------|
| **Pages** | DashboardPage, UsersPage, UserDetailPage, HelpersPage, HelperDetailPage, TasksPage, TaskDetailPage, TransactionsPage, WalletsPage, DisputesPage, AnalyticsPage |
| **Components** | DashboardStats, UserTable, HelperTable, TaskTable, TransactionTable, WalletTable, DisputeTable, AnalyticsChart, AdminFilters |
| **Hooks** | useDashboard, useAdminUsers, useAdminHelpers, useAdminTasks, useAdminTransactions, useAdminWallets, useAdminDisputes |
| **Service** | admin.service.ts |

---

## 9. PWA ARCHITECTURE

### Manifest Configuration

```json
{
  "name": "BantuinYuk",
  "short_name": "BantuinYuk",
  "description": "Marketplace outsourcing task",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#3b82f6",
  "orientation": "portrait-primary",
  "icons": [
    { "src": "/icons/icon-192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/icons/icon-512.png", "sizes": "512x512", "type": "image/png" }
  ]
}
```

### Service Worker Strategy

| Strategy | Purpose | Implementation |
|----------|---------|----------------|
| **Cache First** | Static assets (CSS, JS, images) | Precache on install |
| **Network First** | API calls | Fallback to cache if offline |
| **Stale While Revalidate** | Avatars, thumbnails | Show cache, update in background |

### Caching Rules

```
Static Assets:
  - HTML, CSS, JS: Cache First
  - Images: Cache First
  - Fonts: Cache First

API Calls:
  - GET /auth/me: Network First
  - GET /tasks: Network First (cache last response)
  - POST/PUT/DELETE: Network Only
  - GET /notifications: Stale While Revalidate

Offline Fallback:
  - /offline.html (static page)
```

### Offline Mode

| Feature | Works Offline | Notes |
|---------|---------------|-------|
| View cached tasks | ✅ | Last fetched data |
| View cached profile | ✅ | Last fetched data |
| Create task | ❌ | Requires network |
| Submit progress | ❌ | Requires network |
| Upload attachment | ❌ | Requires network |
| Make payment | ❌ | Requires network |
| View notifications | ✅ | Last fetched data |

### Install Prompt

```typescript
// hooks/useInstallPrompt.ts
- Listen for beforeinstallprompt event
- Store event for later use
- Show install button when available
- Track installation status
```

### Update Strategy

```typescript
// When new version available:
1. Show "Update Available" toast
2. On user click, reload page
3. Service worker activates new version
4. Clear old caches
```

---

## 10. UI ARCHITECTURE

### Layout Structure

```
┌─────────────────────────────────────────────────────────────┐
│                        Header                                │
│  [Logo]                              [Notification] [Avatar] │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│                        Main Content                          │
│                                                              │
│                                                              │
│                                                              │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│                    Bottom Nav (Mobile)                        │
│  [Home]  [Tasks]  [Profile]  [Wallet]  [More]               │
└─────────────────────────────────────────────────────────────┘
```

### Layout Variants

#### User Layout
```
Header: Logo | Tasks | Wallet | Profile
Bottom Nav: Home | Tasks | Create | Notifications | Profile
```

#### Helper Layout
```
Header: Logo | Available | My Tasks | Profile
Bottom Nav: Home | Tasks | Available | Notifications | Profile
```

#### Admin Layout
```
Sidebar: Dashboard | Users | Helpers | Tasks | Disputes | Transactions | Settings
Header: Logo | Search | Notifications | Admin Avatar
```

### Component States

#### Loading State
```
┌──────────────────────────┐
│    [Spinner] Loading...   │
└──────────────────────────┘
```

#### Empty State
```
┌──────────────────────────┐
│      [Icon]              │
│   No tasks found         │
│   Create your first task │
│      [Button]            │
└──────────────────────────┘
```

#### Error State
```
┌──────────────────────────┐
│      [!] Error           │
│   Something went wrong   │
│      [Retry]             │
└──────────────────────────┘
```

---

## 11. FORM STRATEGY

### React Hook Form Pattern

```typescript
// Pattern: Form with validation
const form = useForm<FormData>({
  resolver: zodResolver(schema),
  defaultValues: { ... }
});

// Pattern: Async submission
const onSubmit = form.handleSubmit(async (data) => {
  await mutation.mutateAsync(data);
});
```

### Validation Schemas (Zod)

```typescript
// Common schemas
LoginSchema: email, password
RegisterSchema: name, email, phone, password
TaskCreateSchema: title, description, price, category_id, deadline_start, deadline_end
ReviewSchema: rating (1-5), review (optional)
WithdrawSchema: amount
DisputeSchema: task_id, reason, description
```

---

## 12. DESIGN SYSTEM

### Tailwind Configuration

```javascript
// tailwind.config.js
- Colors: Primary (blue), Secondary (gray), Success, Warning, Danger
- Font: Inter (modern, readable)
- Breakpoints: sm(640), md(768), lg(1024), xl(1280)
- Border radius: rounded-lg (default)
```

### shadcn/ui Components

| Component | Usage |
|-----------|-------|
| Button | All actions |
| Card | Task cards, stats |
| Dialog | Confirmations, forms |
| Dropdown Menu | User menu, actions |
| Form | All forms |
| Input | Text inputs |
| Select | Dropdowns |
| Table | Admin tables |
| Toast | Notifications |
| Badge | Status badges |
| Avatar | User avatars |
| Tabs | Detail pages |
| Pagination | List pages |

---

## 13. RESPONSIVE STRATEGY

### Breakpoints

| Breakpoint | Target | Layout |
|------------|--------|--------|
| `< 640px` | Mobile | Single column, bottom nav |
| `640-768px` | Large Mobile | Single column, bottom nav |
| `768-1024px` | Tablet | Two columns, sidebar hidden |
| `> 1024px` | Desktop | Full layout, sidebar visible |

### Mobile First Approach

```
1. Design for 320px width first
2. Add responsive utilities for larger screens
3. Test on real Android devices
4. Optimize touch targets (min 44px)
```

### Touch Optimization

```
- Minimum touch target: 44px x 44px
- Spacing between targets: 8px minimum
- Swipe gestures for navigation
- Pull to refresh for lists
```

---

## 14. SECURITY STRATEGY

### Frontend Security Checklist

| Security | Implementation |
|----------|----------------|
| **Token Storage** | Memory (Zustand) + sessionStorage |
| **Route Protection** | AuthGuard, RoleGuard, GuestGuard |
| **XSS Prevention** | React auto-escaping + DOMPurify for rich text |
| **CSRF** | SameSite cookies (if using cookies) |
| **File Upload** | Client-side validation + server validation |
| **API Error Handling** | Interceptor catches 401, redirects to login |
| **Sensitive Data** | Never store passwords, tokens in memory only |
| **HTTPS** | Enforce in production |

### Token Flow

```
1. Login → Token received
2. Token stored in Zustand (memory)
3. Token persisted to sessionStorage (survives refresh)
4. Token added to every request via interceptor
5. 401 response → Clear token → Redirect to login
```

### Error Handling

```typescript
// Global error handler
- 400: Show validation errors
- 401: Logout, redirect to /login
- 403: Show "Access Denied" message
- 404: Show "Not Found" page
- 409: Show conflict message
- 422: Show validation errors
- 500: Show "Server Error" page
```

---

## 15. ENVIRONMENT VARIABLES

```env
# .env.example
VITE_API_URL=http://bantuinYuk.test/api/v1
VITE_APP_NAME=BantuinYuk
VITE_APP_VERSION=1.0.0
```

---

## 16. DEVELOPMENT ROADMAP

### Sprint 13.1 - Project Setup (Day 1-2)

| Task | Complexity |
|------|------------|
| Initialize Vite + React + TypeScript | Low |
| Install dependencies (Tailwind, shadcn, Zustand, TanStack Query, React Router, Axios) | Low |
| Configure Tailwind + shadcn/ui | Low |
| Setup folder structure | Low |
| Configure ESLint + Prettier | Low |
| Setup API service with interceptors | Medium |
| Setup Zustand stores | Medium |
| Setup React Router with guards | Medium |
| Create layout components | Medium |

**Sprint 13.1 Total: Medium**

### Sprint 13.2 - Authentication UI (Day 3-4)

| Task | Complexity |
|------|------------|
| LoginPage with form | Low |
| RegisterPage with form | Low |
| Auth store integration | Medium |
| API integration (login, register) | Medium |
| Auth guards implementation | Medium |
| Token persistence | Low |
| Profile display | Low |

**Sprint 13.2 Total: Medium**

### Sprint 13.3 - User Module (Day 5-8)

| Task | Complexity |
|------|------------|
| User dashboard | Medium |
| Task list with filters | High |
| Task detail page | High |
| Create task form | High |
| My tasks page | Medium |
| Task status updates | Medium |
| Review creation | Medium |

**Sprint 13.3 Total: High**

### Sprint 13.4 - Helper Module (Day 9-12)

| Task | Complexity |
|------|------------|
| Helper dashboard | Medium |
| Available tasks | High |
| Helper tasks list | Medium |
| Accept/Start/Submit task | High |
| Profile management | Medium |
| Location update | Low |
| Helper stats | Medium |

**Sprint 13.4 Total: High**

### Sprint 13.5 - Wallet + Notification (Day 13-15)

| Task | Complexity |
|------|------------|
| Wallet summary | Medium |
| Transaction history | Medium |
| Withdraw form | Medium |
| Release payment | Medium |
| Notification list | Low |
| Notification bell | Medium |
| Mark read | Low |

**Sprint 13.5 Total: Medium**

### Sprint 13.6 - Admin Dashboard (Day 16-19)

| Task | Complexity |
|------|------------|
| Admin layout with sidebar | Medium |
| Dashboard stats | Medium |
| User management | High |
| Helper management | High |
| Task management | High |
| Transaction management | Medium |
| Dispute management | High |
| Wallet management | Medium |

**Sprint 13.6 Total: High**

### Sprint 13.7 - PWA Optimization (Day 20-21)

| Task | Complexity |
|------|------------|
| PWA manifest | Low |
| Service worker setup | Medium |
| Offline fallback | Medium |
| Cache strategies | Medium |
| Install prompt | Low |
| Update notification | Low |

**Sprint 13.7 Total: Medium**

### Sprint 13.8 - Integration Testing (Day 22-24)

| Task | Complexity |
|------|------------|
| API integration testing | High |
| Auth flow testing | Medium |
| Role-based routing testing | Medium |
| Form validation testing | Medium |
| Error handling testing | Medium |
| Performance optimization | Medium |
| Bug fixes | Variable |

**Sprint 13.8 Total: High**

---

## 17. SUMMARY

### Final Tech Stack

| Category | Technology |
|----------|------------|
| Framework | React 18 + Vite |
| Language | TypeScript |
| State | Zustand |
| Forms | React Hook Form |
| Data Fetching | TanStack Query |
| Styling | Tailwind CSS + shadcn/ui |
| Routing | React Router v6 |
| HTTP | Axios |
| PWA | vite-plugin-pwa |

### Key Metrics

| Metric | Value |
|--------|-------|
| Total Pages | ~30 |
| Total Components | ~60 |
| Total Hooks | ~25 |
| Total Services | 10 |
| Total Stores | 3 |
| Estimated Sprints | 8 |

---

**Document Ready for Sprint 13 Implementation**
