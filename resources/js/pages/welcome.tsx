import { AspectRatio } from '@/components/ui/aspect-ratio';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Pagination, PaginationContent, PaginationItem, PaginationLink, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import { Sheet, SheetClose, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { motion } from 'framer-motion';
import { Menu, Search } from 'lucide-react';
import { useState } from 'react';

// ---------------------------------------------------------------------------
// Dummy data – replace with fetch() / CMS integration
const posts = [
    {
        id: 1,
        title: 'Memulai dengan shadcn/ui',
        excerpt: 'Pelajari bagaimana memanfaatkan komponen shadcn/ui untuk membangun antarmuka modern dengan cepat.',
        author: 'Agung Dharma',
        date: '2025‑05‑01',
        image: 'https://source.unsplash.com/random/1600x900?ui',
        category: 'Frontend',
    },
    {
        id: 2,
        title: 'Mengenal Tailwind CSS v4',
        excerpt: 'Tailwind CSS v4 hadir dengan fitur baru yang membuat proses styling semakin menyenangkan.',
        author: 'Rahmat Hidayat',
        date: '2025‑05‑10',
        image: 'https://source.unsplash.com/random/1600x900?tailwind',
        category: 'CSS',
    },
    {
        id: 3,
        title: 'Tips Optimasi React untuk Performa',
        excerpt: 'Kiat‑kiat praktis untuk meningkatkan performa aplikasi React Anda.',
        author: 'Dewi Saraswati',
        date: '2025‑05‑15',
        image: 'https://source.unsplash.com/random/1600x900?react',
        category: 'React',
    },
    {
        id: 4,
        title: 'Membangun Blog dengan Next.js 15',
        excerpt: 'Panduan lengkap membuat blog powerful menggunakan Next.js versi terbaru.',
        author: 'Andi Wijaya',
        date: '2025‑05‑22',
        image: 'https://source.unsplash.com/random/1600x900?nextjs',
        category: 'Full‑Stack',
    },
    {
        id: 5,
        title: 'State Management Modern: Jotai vs Zustand',
        excerpt: 'Mana yang lebih cocok untuk proyek Anda? Mari kita bandingkan dua library populer ini.',
        author: 'Sinta Lestari',
        date: '2025‑05‑28',
        image: 'https://source.unsplash.com/random/1600x900?state',
        category: 'React',
    },
    {
        id: 6,
        title: 'Deploy Cepat ke Vercel dengan GitHub Actions',
        excerpt: 'Automasi CI/CD ke Vercel tanpa repot menggunakan GitHub Actions.',
        author: 'Budi Santoso',
        date: '2025‑06‑05',
        image: 'https://source.unsplash.com/random/1600x900?vercel',
        category: 'DevOps',
    },
];

const categories = ['Frontend', 'CSS', 'React', 'Full‑Stack', 'DevOps'];
// ---------------------------------------------------------------------------
function FeaturedPost({ post }) {
    return (
        <motion.section initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.9 }} className="relative isolate mb-16">
            <AspectRatio ratio={16 / 7} className="overflow-hidden rounded-3xl">
                <img src={post.image} alt={post.title} className="h-full w-full scale-[1.02] object-cover object-center" />
                <div className="from-background/80 via-background/60 to-background/30 absolute inset-0 bg-gradient-to-r" />
                <div className="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                    <Badge className="mb-4" variant="secondary">
                        {post.category}
                    </Badge>
                    <h1 className="max-w-2xl text-4xl font-extrabold tracking-tight drop-shadow-lg md:text-5xl">{post.title}</h1>
                    <p className="text-muted-foreground mt-4 max-w-3xl text-lg md:text-xl">{post.excerpt}</p>
                    <Button size="lg" className="mt-8">
                        Baca Selengkapnya
                    </Button>
                </div>
            </AspectRatio>
        </motion.section>
    );
}

function PostCard({ post, large = false }) {
    return (
        <Card
            key={post.id}
            className={`group bg-background/90 overflow-hidden rounded-2xl shadow-md backdrop-blur-sm transition hover:shadow-xl ${large ? 'lg:col-span-2' : ''}`}
        >
            <motion.div
                initial={{ scale: 1, opacity: 0 }}
                whileInView={{ scale: 1, opacity: 1 }}
                viewport={{ once: true }}
                className="overflow-hidden"
            >
                <AspectRatio ratio={16 / 9}>
                    <img
                        src={post.image}
                        alt={post.title}
                        className="h-full w-full object-cover object-center transition-transform duration-500 group-hover:scale-110"
                    />
                </AspectRatio>
            </motion.div>

            <CardContent className="space-y-4 p-6">
                <div className="text-muted-foreground flex items-center gap-2 text-xs">
                    <time>{post.date}</time>
                    <Badge variant="secondary">{post.category}</Badge>
                </div>
                <CardTitle className="hover:text-primary line-clamp-2 transition-colors">{post.title}</CardTitle>
                <CardDescription className="line-clamp-3">{post.excerpt}</CardDescription>
            </CardContent>
            <CardFooter className="flex items-center gap-3 p-6 pt-0">
                <Avatar className="h-8 w-8">
                    <AvatarImage src={`https://i.pravatar.cc/40?u=${post.author}`} />
                    <AvatarFallback>{post.author.charAt(0)}</AvatarFallback>
                </Avatar>
                <span className="text-muted-foreground text-sm">{post.author}</span>
            </CardFooter>
        </Card>
    );
}

export default function BlogPage() {
    const [query, setQuery] = useState('');
    const [page, setPage] = useState(1);
    const pageSize = 5; // sedikit lebih kecil agar grid tampak rapi

    const filtered = posts.filter((p) => p.title.toLowerCase().includes(query.toLowerCase()));
    const paginated = filtered.slice((page - 1) * pageSize, page * pageSize);
    const pageCount = Math.ceil(filtered.length / pageSize);

    const featured = filtered[0];
    const restPosts = paginated.filter((p) => p.id !== featured.id);

    return (
        <div className="bg-muted/40 flex min-h-screen flex-col font-sans">
            {/* —————————————————— Header / Navbar —————————————————— */}
            <header className="bg-background/70 supports-[backdrop-filter]:bg-background/60 sticky top-0 z-50 w-full border-b backdrop-blur">
                <div className="container flex h-16 items-center justify-between">
                    {/* Mobile menu */}
                    <Sheet>
                        <SheetTrigger asChild>
                            <Button variant="ghost" size="icon" className="lg:hidden">
                                <Menu className="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" className="pr-0">
                            <nav className="space-y-4 p-6">
                                {[
                                    { href: '#', label: 'Home' },
                                    { href: '#', label: 'About' },
                                    { href: '#', label: 'Contact' },
                                ].map((link) => (
                                    <SheetClose asChild key={link.label}>
                                        <a href={link.href} className="hover:text-primary block text-lg font-medium">
                                            {link.label}
                                        </a>
                                    </SheetClose>
                                ))}
                            </nav>
                        </SheetContent>
                    </Sheet>

                    {/* Logo */}
                    <a href="#" className="text-2xl font-bold tracking-tight">
                        My‑Blog
                    </a>

                    {/* Desktop nav */}
                    <nav className="hidden items-center gap-8 text-sm font-medium lg:flex">
                        <a href="#" className="hover:text-primary transition-colors">
                            Home
                        </a>
                        <a href="#" className="hover:text-primary transition-colors">
                            About
                        </a>
                        <a href="#" className="hover:text-primary transition-colors">
                            Contact
                        </a>
                    </nav>
                </div>
            </header>

            {/* —————————————————— Featured Post / Hero —————————————————— */}
            {featured && <FeaturedPost post={featured} />}

            {/* —————————————————— Main Content —————————————————— */}
            <main className="container grid flex-1 grid-cols-1 gap-10 pb-14 lg:grid-cols-12">
                {/* ——— Posts Grid ——— */}
                <section className="space-y-10 lg:col-span-8">
                    <div className="grid auto-rows-fr gap-8 sm:grid-cols-2 xl:grid-cols-2">
                        {restPosts.map((post, idx) => (
                            <PostCard key={post.id} post={post} large={idx === 0} />
                        ))}
                    </div>

                    {/* Pagination */}
                    {pageCount > 1 && (
                        <Pagination className="justify-center">
                            <PaginationContent>
                                <PaginationItem>
                                    <PaginationPrevious
                                        onClick={() => setPage((p) => Math.max(1, p - 1))}
                                        className={page === 1 ? 'pointer-events-none opacity-40' : ''}
                                    />
                                </PaginationItem>
                                {Array.from({ length: pageCount }).map((_, i) => (
                                    <PaginationItem key={i}>
                                        <PaginationLink isActive={page === i + 1} onClick={() => setPage(i + 1)}>
                                            {i + 1}
                                        </PaginationLink>
                                    </PaginationItem>
                                ))}
                                <PaginationItem>
                                    <PaginationNext
                                        onClick={() => setPage((p) => Math.min(pageCount, p + 1))}
                                        className={page === pageCount ? 'pointer-events-none opacity-40' : ''}
                                    />
                                </PaginationItem>
                            </PaginationContent>
                        </Pagination>
                    )}
                </section>

                {/* ——— Sidebar ——— */}
                <aside className="order-first space-y-8 lg:order-none lg:col-span-4">
                    {/* Search */}
                    <Card className="rounded-2xl">
                        <CardHeader>
                            <CardTitle>Pencarian</CardTitle>
                        </CardHeader>
                        <CardContent className="relative">
                            <Search className="text-muted-foreground absolute top-3 left-3 h-4 w-4" />
                            <Input placeholder="Cari artikel ..." className="pl-10" value={query} onChange={(e) => setQuery(e.target.value)} />
                        </CardContent>
                    </Card>

                    {/* Categories */}
                    <Card className="rounded-2xl">
                        <CardHeader>
                            <CardTitle>Kategori</CardTitle>
                        </CardHeader>
                        <CardContent className="flex flex-wrap gap-3">
                            {categories.map((cat) => (
                                <Badge key={cat} variant="outline" className="hover:bg-primary/10 cursor-pointer">
                                    {cat}
                                </Badge>
                            ))}
                        </CardContent>
                    </Card>

                    {/* Recent Posts */}
                    <Card className="rounded-2xl">
                        <CardHeader>
                            <CardTitle>Artikel Terbaru</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {posts.slice(0, 4).map((post) => (
                                <div key={post.id} className="flex items-start gap-3">
                                    <AspectRatio ratio={4 / 3} className="w-28 overflow-hidden rounded-md">
                                        <img src={post.image} alt={post.title} className="h-full w-full object-cover" />
                                    </AspectRatio>
                                    <div>
                                        <h4 className="hover:text-primary line-clamp-2 text-sm font-medium transition-colors">{post.title}</h4>
                                        <time className="text-muted-foreground text-xs">{post.date}</time>
                                    </div>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                </aside>
            </main>

            {/* —————————————————— Footer —————————————————— */}
            <footer className="bg-background/70 border-t py-8 backdrop-blur">
                <div className="text-muted-foreground container grid gap-6 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    <div className="space-y-3">
                        <h3 className="font-semibold">About</h3>
                        <p>My‑Blog adalah platform berbagi insight tentang teknologi web & pemrograman.</p>
                    </div>
                    <div className="space-y-3">
                        <h3 className="font-semibold">Quick Links</h3>
                        <ul className="space-y-2">
                            <li>
                                <a href="#" className="hover:text-primary">
                                    Privacy Policy
                                </a>
                            </li>
                            <li>
                                <a href="#" className="hover:text-primary">
                                    Terms & Conditions
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div className="flex items-center justify-between sm:col-span-2 lg:col-span-1 lg:block lg:text-right">
                        <span>© {new Date().getFullYear()} My‑Blog. Dibuat dengan ❤ dan shadcn/ui.</span>
                    </div>
                </div>
            </footer>
        </div>
    );
}
