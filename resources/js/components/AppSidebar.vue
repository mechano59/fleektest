<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3'; // Import usePage
import { BookOpen, Folder, LampCeiling, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

// Use usePage() to get access to the page props
const page = usePage();

const mainNavItems = computed(() => {
    // Access user role through the usePage() hook
    const userRole = (page.props.auth as any)?.user?.role; // Type assertion for safety
    return [
        {
            title: 'Posts',
            href: '/',
            icon: LampCeiling,
        },
        {
            title: 'Dashboard',
            href: userRole === 'admin' ? '/admin/dashboard' : '/dashboard',
            icon: LayoutGrid,
        },
    ];
});

const footerNavItems: NavItem[] = []; // Simplified

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link 
                            v-if="(page.props.auth as any)?.user"
                            :href="(page.props.auth as any)?.user?.role === 'admin' ? route('admin.dashboard') : route('dashboard')"
                        >
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
