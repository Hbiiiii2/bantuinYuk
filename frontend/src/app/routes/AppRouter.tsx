import { createBrowserRouter, RouterProvider } from 'react-router-dom'
import { GuestGuard } from './guards/GuestGuard'
import { ProtectedGuard } from './guards/ProtectedGuard'
import { RoleGuard } from './guards/RoleGuard'

import { AuthLayout } from '@/app/layouts/AuthLayout'
import { UserLayout } from '@/app/layouts/UserLayout'
import { HelperLayout } from '@/app/layouts/HelperLayout'
import { AdminLayout } from '@/app/layouts/AdminLayout'

import { LoginPage } from '@/features/auth/components/LoginPage'
import { RegisterPage } from '@/features/auth/components/RegisterPage'
import { NotFoundPage } from '@/components/shared/NotFoundPage'
import { UnauthorizedPage } from '@/components/shared/UnauthorizedPage'

import { AdminDashboardPage } from '@/features/auth/components/AdminDashboardPlaceholder'

import { 
  UserDashboard,
  TaskListPage,
  TaskDetailPage,
  CreateTaskPage,
  TaskHistoryPage
} from '@/features/tasks'

import {
  HelperDashboard,
  AvailableTasksPage,
  HelperTaskDetail,
  CurrentTaskPage,
  HelperProfile
} from '@/features/helper'

const router = createBrowserRouter([
  {
    path: '/',
    element: <GuestGuard />,
    children: [
      {
        element: <AuthLayout />,
        children: [
          {
            path: 'login',
            element: <LoginPage />
          },
          {
            path: 'register',
            element: <RegisterPage />
          }
        ]
      }
    ]
  },
  {
    path: '/',
    element: <ProtectedGuard />,
    children: [
      {
        path: 'user',
        element: <RoleGuard allowedRoles={['user']} />,
        children: [
          {
            element: <UserLayout />,
            children: [
              {
                path: 'dashboard',
                element: <UserDashboard />
              },
              {
                path: 'tasks',
                element: <TaskListPage />
              },
              {
                path: 'tasks/create',
                element: <CreateTaskPage />
              },
              {
                path: 'tasks/:id',
                element: <TaskDetailPage />
              },
              {
                path: 'history',
                element: <TaskHistoryPage />
              }
            ]
          }
        ]
      },
      {
        path: 'helper',
        element: <RoleGuard allowedRoles={['helper']} />,
        children: [
          {
            element: <HelperLayout />,
            children: [
              {
                path: 'dashboard',
                element: <HelperDashboard />
              },
              {
                path: 'tasks',
                element: <AvailableTasksPage />
              },
              {
                path: 'tasks/:id',
                element: <HelperTaskDetail />
              },
              {
                path: 'current-task',
                element: <CurrentTaskPage />
              },
              {
                path: 'profile',
                element: <HelperProfile />
              }
            ]
          }
        ]
      },
      {
        path: 'admin',
        element: <RoleGuard allowedRoles={['admin']} />,
        children: [
          {
            element: <AdminLayout />,
            children: [
              {
                path: 'dashboard',
                element: <AdminDashboardPage />
              }
            ]
          }
        ]
      }
    ]
  },
  {
    path: '/unauthorized',
    element: <UnauthorizedPage />
  },
  {
    path: '*',
    element: <NotFoundPage />
  }
])

export function AppRouter() {
  return <RouterProvider router={router} />
}
