<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import Pusher from 'pusher-js';
import { usePage } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'; // Corrected import
// --- Pusher Setup ---
let pusher: Pusher | null = null;
let adminChannel: any = null;
const adminNotifications = ref<any[]>([]); // Reactive array for admin notifications

onMounted(() => {
  const user = usePage().props.auth.user as any; // Type assertion

  // Initialize Pusher
  pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
      encrypted: true,
    authEndpoint: '/broadcasting/auth',
  });


  // Admin-specific channel subscription
  if (user.isAdmin) {
    const adminChannelName = 'private-admin-channel';

    adminChannel = pusher.subscribe(adminChannelName);

    adminChannel.bind('pusher:subscription_succeeded', () => {
    });
    adminChannel.bind('pusher:subscription_error', (status: any) => {
      console.error('Subscription to admin channel failed with status:', status); // keep error
    });

    adminChannel.bind('App\\Events\\PostCreated', (data: any) => {
        const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(error => {
            console.log('Could not play notification sound', error); // Keep for debugging sound
            });
      // Construct the notification object
      const newNotification = {
        id: Date.now(),
        user_id: data.post.user_id,
        post_id: data.post.id,
        type: 'post_pending',
        message: `${data.post.user.name} has created a new post "${data.post.title}" that needs approval.`,
        is_read: false,
        created_at: new Date().toISOString(),
        post: data.post,
      };
      adminNotifications.value.unshift(newNotification);
    });
  }
});

onUnmounted(() => {
  if (adminChannel) {
    pusher?.unsubscribe(adminChannel.name);
  }
  pusher?.disconnect();
  pusher = null;
});
</script>

<template>
  <AppSidebarLayout :breadcrumbs="breadcrumbs">
    <slot />
  </AppSidebarLayout>
</template>
