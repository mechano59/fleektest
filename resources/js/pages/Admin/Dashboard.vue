<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import axios from 'axios';

const props = defineProps<{
  pendingPosts: Array<{
    id: number;
    title: string;
    content: string;
    category: {
      category_name: string;
    };
    user: {
      name: string;
    };
    created_at: string;
  }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/admin/dashboard',
  },
];

const approvePost = async (postId: number) => {
  try {
    await axios.post(route('admin.posts.approve', postId));
    window.location.reload(); // Refresh to update the UI
  } catch (error) {
    console.error('Error approving post', error);
  }
};

const rejectPost = async (postId: number) => {
  try {
    await axios.post(route('admin.posts.reject', postId));
    window.location.reload(); // Refresh to update the UI
  } catch (error) {
    console.error('Error rejecting post', error);
  }
};
</script>

<template>
  <Head title="Dashboard Admin" />

  <AppLayout :breadcrumbs="breadcrumbs">
    
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <Card>
        <CardHeader>
          <CardTitle>Pending Posts</CardTitle>
          <CardDescription>Review and moderate pending posts</CardDescription>
        </CardHeader>
        <CardContent>
          <div v-if="pendingPosts.length === 0" class="text-center p-4 text-muted-foreground">
            No pending posts to review
          </div>
          
          <div v-else class="space-y-4">
            <Card v-for="post in pendingPosts" :key="post.id" class="border border-gray-200 p-4">
              <div class="flex justify-between">
                <div>
                  <h3 class="text-lg font-medium">{{ post.title }}</h3>
                  <p class="text-sm text-muted-foreground">By {{ post.user.name }} in {{ post.category.category_name }}</p>
                  <p class="text-sm text-muted-foreground">{{ new Date(post.created_at).toLocaleString() }}</p>
                </div>
                <div class="flex gap-2">
                  <Button variant="default" @click="approvePost(post.id)">Approve</Button>
                  <Button variant="destructive" @click="rejectPost(post.id)">Reject</Button>
                </div>
              </div>
              
              <div class="mt-2">
                <p class="text-sm line-clamp-3">{{ post.content }}</p>
              </div>
            </Card>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>