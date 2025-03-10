<script setup lang="ts">
import { ref, watch } from "vue";
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Alert,
    AlertDescription,
    AlertTitle,
} from '@/components/ui/alert';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Info, X } from 'lucide-vue-next';
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { AspectRatio } from '@/components/ui/aspect-ratio';
import { Button } from '@/components/ui/button';

// Type definitions
interface Post {
    id: number;
    title: string;
    content: string;
    image_path: string | null;
    category: {
        category_id: number;
        category_name: string;
    };
    user: {
        id: number;
        name: string;
        profile_photo_url?: string;
    };
    created_at: string;
}

interface Category {
    id: number;
    name: string;
}

interface Author {
    id: number;
    name: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    posts: {
        data: Post[];
        links: PaginationLink[];
        meta: {
            current_page: number;
            last_page: number;
            from: number;
            to: number;
            total: number;
        };
    };
    categories: Category[];
    authors: Author[];
    filters: {
        search?: string;
        category?: string;
        author?: string;
        sort?: string;
    };
}>();

const searchQuery = ref(props.filters.search || "");

const updateSearch = () => {
    updateFilter("search", searchQuery.value);
};

// Computed properties
const hasActiveFilters = computed(() => {
    return !!(
        props.filters.search ||
        props.filters.category ||
        props.filters.author ||
        props.filters.sort
    );
});

const getCategoryName = (categoryId: string) => {
    return props.categories.find(c => c.id === Number(categoryId))?.name || '';
};

const getAuthorName = (authorId: string) => {
    return props.authors.find(a => a.id === Number(authorId))?.name || '';
};

const updateFilter = (filterName: string, value: string) => {
    const query = { ...props.filters, [filterName]: value || undefined };
    delete query.page; // Remove page from query

    router.get(route('home'), query, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

const updatePage = (url) => {
    router.get(url, {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

const clearAllFilters = () => {
    router.get(route('home'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

const defaultAvatarPath = '/images/default-avatar.png'; // Updated default avatar path
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a] lg:justify-center lg:p-8">
        <header class="not-has-[nav]:hidden mb-6 w-full max-w-4xl text-sm">
            <nav class="flex items-center justify-end gap-4">
                <Link v-if="$page.props.auth.user"
                    :href="$page.props.auth.user.role === 'admin' ? route('admin.dashboard') : route('dashboard')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]">
                    Log in
                    </Link>
                    <Link :href="route('register')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                    Register
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Filters Section -->
        <div class="w-full max-w-4xl mb-8 space-y-4">
            <!-- Search Input -->
            <Input
                v-model="searchQuery"
                placeholder="Search posts..."
                @input="updateSearch"
            />

            <!-- Filter Controls -->
            <div class="flex flex-col sm:flex-row gap-4 w-full">
                <!-- Category Filter -->
                <div class="w-full sm:w-1/3">
                    <Select :model-value="props.filters.category?.toString() || ''"
                        @update:model-value="value => updateFilter('category', value)">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Filter by category" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Categories</SelectItem>
                            <SelectItem v-for="category in props.categories" :key="category.id" :value="category.id.toString()">
                                {{ category.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Author Filter -->
                <div class="w-full sm:w-1/3">
                    <Select :model-value="props.filters.author?.toString() || ''"
                        @update:model-value="value => updateFilter('author', value)">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Filter by author" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Authors</SelectItem>
                            <SelectItem v-for="author in props.authors" :key="author.id" :value="author.id.toString()">
                                {{ author.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Sort Filter -->
                <div class="w-full sm:w-1/3">
                    <Select :model-value="props.filters.sort || 'latest'"
                        @update:model-value="value => updateFilter('sort', value)">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Sort by" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="latest">Newest First</SelectItem>
                            <SelectItem value="oldest">Oldest First</SelectItem>
                            <SelectItem value="title_asc">Title (A-Z)</SelectItem>
                            <SelectItem value="title_desc">Title (Z-A)</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <!-- Active Filters -->
            <div v-if="hasActiveFilters" class="flex items-center gap-3">
                <span class="text-sm text-muted-foreground">Active filters:</span>
                <div class="flex flex-wrap gap-2">
                    <Badge v-if="props.filters.search" variant="outline" class="cursor-pointer"
                        @click="updateFilter('search', '')">
                        Search: {{ props.filters.search }}
                        <X class="h-3 w-3 ml-1" />
                    </Badge>

                    <Badge v-if="props.filters.category" variant="outline" class="cursor-pointer"
                        @click="updateFilter('category', '')">
                        Category: {{ getCategoryName(props.filters.category) }}
                        <X class="h-3 w-3 ml-1" />
                    </Badge>

                    <Badge v-if="props.filters.author" variant="outline" class="cursor-pointer"
                        @click="updateFilter('author', '')">
                        Author: {{ getAuthorName(props.filters.author) }}
                        <X class="h-3 w-3 ml-1" />
                    </Badge>

                    <Badge v-if="props.filters.sort" variant="outline" class="cursor-pointer"
                        @click="updateFilter('sort', '')">
                        Sort: {{
                            {
                                latest: 'Newest First',
                                oldest: 'Oldest First',
                                title_asc: 'Title (A-Z)',
                                title_desc: 'Title (Z-A)'
                            }[props.filters.sort]
                        }}
                        <X class="h-3 w-3 ml-1" />
                    </Badge>

                    <Button variant="ghost" size="sm" class="h-8 text-muted-foreground" @click="clearAllFilters">
                        Clear all
                    </Button>
                </div>
            </div>
        </div>

        <!-- Posts Section -->
        <div class="w-full max-w-4xl space-y-8">
            <!-- Posts List -->
            <Card v-for="post in props.posts.data" :key="post.id" class="group hover:shadow-lg transition-shadow">
                <CardHeader class="flex flex-row items-center gap-4 pb-2">
                    <Avatar>
                        <AvatarImage :src="post.user.profile_photo_url || defaultAvatarPath" />
                        <AvatarFallback>{{ post.user.name.charAt(0) }}</AvatarFallback>
                    </Avatar>
                    <div class="flex flex-col space-y-1">
                        <span class="text-sm font-medium">{{ post.user.name }}</span>
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            <time>{{ new Date(post.created_at).toLocaleDateString() }}</time>
                            <span class="text-xs">•</span>
                            <Badge variant="outline" class="rounded-full">
                                {{ post.category.category_name }}
                            </Badge>
                        </div>
                    </div>
                </CardHeader>

                <CardContent class="space-y-4">
                    <CardTitle class="text-2xl">{{ post.title }}</CardTitle>
                    <p class="text-[#454441] dark:text-[#EDEDEC] leading-relaxed">
                        {{ post.content }}
                    </p>

                    <AspectRatio :ratio="16 / 9" v-if="post.image_path">
                        <img :src="`/${post.image_path}`" alt="Post image"
                            class="rounded-lg object-cover w-full h-full" />
                    </AspectRatio>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div class="flex items-center justify-center gap-2">
                <template v-for="(link, index) in props.posts.links" :key="index">
                    <button v-if="link.url" @click.prevent="updatePage(link.url)"
                        class="px-4 py-2 rounded-md text-sm font-medium" :class="{
                            'bg-primary text-primary-foreground': link.active,
                            'hover:bg-accent hover:text-accent-foreground': !link.active,
                        }">
                        <span v-html="link.label" />
                    </button>
                    <span v-else class="px-4 py-2 text-muted-foreground" v-html="link.label" />
                </template>
            </div>

            <!-- Empty State -->
            <Alert v-if="props.posts.data.length === 0" variant="default" class="mt-8">
                <Info class="h-4 w-4" />
                <AlertTitle>No posts found</AlertTitle>
                <AlertDescription>
                    Try adjusting your filters or create the first post!
                </AlertDescription>
            </Alert>
        </div>
    </div>
</template>