<script setup lang="ts">
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import {
  Bell,
  BellRing
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import axios from 'axios';
import Pusher from 'pusher-js';

const props = defineProps<{
  notifications: Array<{
    id: number;
    user_id: number;
    post_id: number;
    type: string;
    message: string;
    is_read: boolean;
    created_at: string;
    post: {
      id: number;
      title: string;
      user: {
        id: number;
        name: string;
      }
    }
  }>;
  userId: number;
  isAdmin: boolean;
}>();

// Local reactive copy of notifications
const localNotifications = ref<any[]>([...props.notifications]);

// Watch for changes in the props.notifications and update the local copy
watch(() => props.notifications, (newNotifications) => {
    localNotifications.value = [...newNotifications];
},
{ deep: true }
);

const unreadCount = computed(() => {
  return localNotifications.value.filter(n => !n.is_read).length;
});

const markAsRead = async (notificationId: number) => {
  try {
    await axios.post(route('notifications.read', notificationId));
    const index = localNotifications.value.findIndex(n => n.id === notificationId);
    if (index !== -1) {
      localNotifications.value[index].is_read = true;
    }
  } catch (error) {
    console.error('Error marking as read', error); // Keep error handling
  }
};

const approvePost = async (postId: number) => {
  try {
    await axios.post(route('admin.posts.approve', postId));
    removeNotificationByPostId(postId);
  } catch (error) {
    console.error('Error approving post', error); // Keep error handling
  }
};

const rejectPost = async (postId: number) => {
  try {
    await axios.post(route('admin.posts.reject', postId));
    removeNotificationByPostId(postId);
  } catch (error) {
    console.error('Error rejecting post', error); // Keep error handling
  }
};

const removeNotificationByPostId = (postId: number) => {
  localNotifications.value = localNotifications.value.filter(n => n.post_id !== postId);
};

let pusher: Pusher | null = null; //for user
let userChannel: any = null;

onMounted(() => {
  // User-specific channel (only for non-admin users)
  if (!props.isAdmin) {
    const channelName = `private-user.${props.userId}`;

     pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
          });

    userChannel = pusher.subscribe(channelName); // Subscribe

    userChannel.bind('pusher:subscription_succeeded', () => {
      // Optional: You might want to keep a success message, even in production
      // console.log('Successfully subscribed to user channel:', channelName);
    });
    userChannel.bind('pusher:subscription_error', (error: any) => {
      console.error('Error subscribing to user channel:', channelName, error); // Keep error handling
    });

    userChannel.bind('post.status.changed', (data: any) => {
      // console.log('Received post.status.changed event:', data); // Removed data logging
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(error => {
              console.error('Could not play notification sound', error); //Keep error handling.
            });
      const notification = {
        id: data.notification_id || Date.now(),
        user_id: props.userId,
        post_id: data.post_id,
        type: `post_${data.status}`,
        message: data.message,
        is_read: false,
        created_at: new Date().toISOString(),
        post: {
          id: data.post_id,
          title: data.title,
          user: {
            id: props.userId,
            name: '',
          },
        },
      };
      localNotifications.value.unshift(notification);
    });
  }
});

onUnmounted(() => {
   if (userChannel) {
      pusher?.unsubscribe(userChannel.name);
      userChannel = null;
  }
    pusher?.disconnect();
    pusher = null;
});
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" size="icon" class="relative">
        <BellRing v-if="unreadCount > 0" class="h-5 w-5" />
        <Bell v-else class="h-5 w-5" />
        <span v-if="unreadCount > 0"
          class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white">
          {{ unreadCount }}
        </span>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end" class="w-80">
      <div class="flex items-center justify-between p-2 border-b">
        <span class="font-medium">Notifications</span>
        <span v-if="unreadCount > 0" class="text-xs text-muted-foreground">{{ unreadCount }} unread</span>
      </div>

      <div v-if="localNotifications.length === 0" class="p-4 text-center text-sm text-muted-foreground">
        No notifications
      </div>

      <div v-else class="max-h-96 overflow-y-auto">
        <DropdownMenuItem v-for="notification in localNotifications" :key="notification.id"
          class="flex flex-col items-start p-3 relative"
          :class="{ 'bg-gray-50 dark:bg-gray-800': !notification.is_read }">
          <div class="w-full">
            <span class="text-sm font-medium" :class="{ 'text-primary': !notification.is_read }">
              {{ notification.message }}
            </span>
          </div>

          <span class="text-xs text-muted-foreground mt-1">
            {{ new Date(notification.created_at).toLocaleString() }}
          </span>

          <button v-if="!notification.is_read" @click.stop="markAsRead(notification.id)"
            class="absolute bottom-2 right-3 text-xs text-green-500 hover:text-green-700 font-medium">
            Mark as read
          </button>

          <div v-if="props.isAdmin && notification.type === 'post_pending'" class="flex gap-2 mt-4 w-full">
            <Button size="sm" variant="default" class="w-1/2" @click.stop="approvePost(notification.post.id)">
              Approve
            </Button>
            <Button size="sm" variant="destructive" class="w-1/2" @click.stop="rejectPost(notification.post.id)">
              Reject
            </Button>
          </div>
        </DropdownMenuItem>
      </div>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
