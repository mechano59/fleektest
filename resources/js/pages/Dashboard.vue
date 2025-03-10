<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed, toRefs, watch, onMounted } from 'vue';
import NotificationList from '@/components/NotificationList.vue';
import { useToast } from '@/components/ui/toast';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

const props = defineProps<{
  categories: Array<{
    category_id: number;
    category_name: string;
  }>;
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
  auth: {
    user: {
      id: number;
      isAdmin: boolean;
    }
  };
  breadcrumbs: BreadcrumbItem[]; // Re-add breadcrumbs prop
}>();

// Make notifications reactive
const { notifications } = toRefs(props);

// Track previous notification count to detect new notifications
const previousNotificationCount = ref(notifications.value.length);
const notificationsUpdated = ref(false);
const toast = useToast(); // Use toast for showing new notification alerts

// Reset notification update indicator after a short delay
const resetNotificationIndicator = () => {
  setTimeout(() => {
    notificationsUpdated.value = false;
  }, 5000);
};

// Watch for new notifications
watch(() => notifications.value.length, (newCount) => {
  if (newCount > previousNotificationCount.value) {
    // We have new notifications
    notificationsUpdated.value = true;

    // Play notification sound (optional)
    playNotificationSound();

    // Show toast notification (if you have a toast component)
    toast.toast({
      title: 'New Notification',
      description: 'You have received a new notification',
      variant: 'default',
    });

    resetNotificationIndicator();
  }
  previousNotificationCount.value = newCount;
});

// Notification sound function
const playNotificationSound = () => {
  const audio = new Audio('/sounds/notification.mp3'); // Make sure this file exists
  audio.volume = 0.5;
  audio.play().catch(error => {
    // Browser might block autoplay, just log the error
    console.log('Could not play notification sound', error);
  });
};

const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.is_read).length;
});

const form = useForm({
  title: '',
  content: '',
  category_id: '',
  image: null as File | null,
});

// Add custom validation states
const titleError = ref('');
const contentError = ref('');
const categoryError = ref('');
const imageError = ref('');
const fileInput = ref<HTMLInputElement | null>(null);

// Track field interaction
const titleTouched = ref(false);
const contentTouched = ref(false);
const categoryTouched = ref(false);

// validation functions (same as before)
const validateTitle = () => {
  if (!titleTouched.value) return true;
  titleError.value = form.title.trim() === '' ? 'Title is required' : '';
  return titleError.value === '';
};

const validateContent = () => {
  if (!contentTouched.value) return true;
  contentError.value = form.content.trim() === '' ? 'Content is required' : '';
  return contentError.value === '';
};

const validateCategory = () => {
  if (!categoryTouched.value) return true;
  categoryError.value = !form.category_id ? 'Please select a category' : '';
  return categoryError.value === '';
};

const validateImage = (file: File | null) => {
  if (!file) return true; // Image is optional

  const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];

  if (!allowedTypes.includes(file.type)) {
    imageError.value = 'Only image files are allowed (JPEG, PNG, GIF, WEBP, SVG)';
    // Clear the file input
    if (fileInput.value) {
      fileInput.value.value = '';
    }
    form.image = null;
    return false;
  }

  // Validate file size (optional, limit to 5MB for example)
  const maxSize = 5 * 1024 * 1024; // 5MB
  if (file.size > maxSize) {
    imageError.value = 'Image size should be less than 5MB';
    if (fileInput.value) {
      fileInput.value.value = '';
    }
    form.image = null;
    return false;
  }

  imageError.value = '';
  return true;
};

const resetForm = () => {
  form.reset();
  titleError.value = '';
  contentError.value = '';
  categoryError.value = '';
  imageError.value = '';
  titleTouched.value = false;
  contentTouched.value = false;
  categoryTouched.value = false;

  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const handleTitleBlur = () => {
    console.log("Title blurred");
  titleTouched.value = true;
  validateTitle();
};

const handleContentBlur = () => {
  contentTouched.value = true;
  validateContent();
};

const handleCategoryChange = () => {
  categoryTouched.value = true;
  validateCategory();
};

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0] || null;

  if (file) {
    if (validateImage(file)) {
      form.image = file;
    }
  } else {
    form.image = null;
    imageError.value = '';
  }
};

const isFormValid = computed(() => {
  const isTitleValid = form.title.trim() !== '';
  const isContentValid = form.content.trim() !== '';
  const isCategoryValid = !!form.category_id;
  const isImageValid = !imageError.value;

  return isTitleValid && isContentValid && isCategoryValid && isImageValid;
});

const submit = () => {
    console.log("Form submitted");
  titleTouched.value = true;
  contentTouched.value = true;
  categoryTouched.value = true;

  validateTitle();
  validateContent();
  validateCategory();

  if (isFormValid.value) {
         console.log("Form is valid, submitting...");
    form.post(route('posts.store'), {
       onSuccess: () => {
        console.log("onsuccess"); // Add this
        // Reset the form after successful submission
        resetForm();
      },
        onError: (errors) => {
        console.log("Form errors:", errors); // Log any validation errors
    },
      forceFormData: true,

    });
  } else {
        console.log("Form is invalid");
    }
};
</script>

<template>
	<Head title="Dashboard" />

	<AppLayout :breadcrumbs="breadcrumbs">
		<!-- Top Bar for Notifications -->

        	<div class="absolute top-4 right-4 z-50">
			<div class="relative" :class="{ 'animate-pulse': notificationsUpdated }">
				<NotificationList :notifications="notifications" :userId="auth.user.id" :isAdmin="auth.user.isAdmin" />
			</div>
		</div>


		<div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
			     
      <div 
        v-if="unreadCount > 0" 
        class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg mb-2 flex justify-between items-center"
      >
        <div class="flex items-center space-x-2">
          <span class="text-blue-600 dark:text-blue-400">
            You have {{ unreadCount }} unread notification{{ unreadCount > 1 ? 's' : '' }}
          </span>
        </div>
      </div>
			<div class="grid auto-rows-min gap-4 md:grid-cols-3">
				<Card class="w-[350px]">
					<CardHeader>
						<CardTitle>Add a new Post</CardTitle>
						<CardDescription>Post something for others to see.</CardDescription>
					</CardHeader>
					<CardContent>
						<form id="create-post-form" @submit.prevent="submit">
							<div class="grid items-center w-full gap-4">
								<div class="flex flex-col space-y-1.5">
									<Label for="title">Post Title</Label>
									<Input id="title" v-model="form.title" placeholder="Title of your post" @blur="handleTitleBlur"
                    @input="titleTouched && validateTitle()" :class="{ 'border-red-500': titleError }" />
									<p v-if="titleError" class="text-red-500 text-sm">{{ titleError }}</p>
								</div>

								<div class="flex flex-col space-y-1.5">
									<Label for="content">Post Content</Label>
									<Textarea id="content" v-model="form.content" placeholder="Post description"
                    @blur="handleContentBlur" @input="contentTouched && validateContent()" :class="{ 'border-red-500': contentError }" />
                  <p v-if="contentError" class="text-red-500 text-sm">{{ contentError }}</p>
								</div>

								<div class="flex flex-col space-y-1.5">
									<Label>Category</Label>
									<Select v-model.number="form.category_id" @update:modelValue="handleCategoryChange">
										<SelectTrigger :class="{ 'border-red-500': categoryError }">
											<SelectValue placeholder="Select a category" />
										</SelectTrigger>
										<SelectContent>
											<SelectGroup>
												<SelectLabel>Categories</SelectLabel>
												<SelectItem v-for="category in categories" :key="category.category_id"
													:value="category.category_id">
													{{ category.category_name }}
												</SelectItem>
											</SelectGroup>
										</SelectContent>
									</Select>
                  <p v-if="categoryError" class="text-red-500 text-sm">{{ categoryError }}</p>
								</div>

								<div class="grid w-full max-w-sm items-center gap-1.5">
									<Label for="image">Image (Optional)</Label>
									<Input id="image" ref="fileInput" type="file" accept="image/*" @change="handleFileChange"
                    :class="{ 'border-red-500': imageError }" />
                  <p v-if="imageError" class="text-red-500 text-sm">{{ imageError }}</p>
                  <p v-if="form.image" class="text-sm text-green-600">
                    Selected: {{ form.image.name }} ({{ (form.image.size / 1024).toFixed(1) }} KB)
                  </p>
								</div>
							</div>
						</form>
					</CardContent>
					<CardFooter class="flex justify-between px-6 pb-6">
						<Button variant="outline" as-child @click="resetForm">
							 <a :href="route('dashboard')">Cancel</a>
						</Button>
						<Button type="submit" form="create-post-form" :disabled="form.processing">
							{{ form.processing ? 'Creating...' : 'Create Post' }}
						</Button>
					</CardFooter>
				</Card>
			</div>
		</div>
	</AppLayout>
</template>
