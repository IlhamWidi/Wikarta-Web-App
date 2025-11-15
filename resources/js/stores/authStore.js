import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export const useAuthStore = create(
    persist(
        (set) => ({
            user: null,
            token: null,
            isAuthenticated: false,

            setAuth: (user, token) => set({
                user: user
                    ? {
                          ...user,
                          permissions: user.permissions || [],
                          role: user.role || user.roles?.[0]?.name || 'User',
                      }
                    : null,
                token,
                isAuthenticated: true,
            }),
            
            logout: () => set({ user: null, token: null, isAuthenticated: false }),
            
            updateUser: (userData) => set((state) => ({ 
                user: { ...state.user, ...userData } 
            })),
        }),
        {
            name: 'agus-provider-auth',
        }
    )
);
