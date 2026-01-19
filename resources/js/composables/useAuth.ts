import { usePage } from '@inertiajs/vue3';

export const useAuth = () => {
    const { auth } = usePage().props; // ✅ Fully typed thanks to augmentation!
    return {
        user: auth.user,
        isOwnerOf(resourceUserId: number): boolean {
            return auth.user.id === resourceUserId;
        },
    };
};
