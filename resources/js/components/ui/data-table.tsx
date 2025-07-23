import React, { useState } from 'react';
import { Table, TableHeader, TableBody, TableCell, TableHead, TableRow } from '@/components/ui/table';
import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';

export type Column<T> = {
    key: string;
    label: string;
    sortable?: boolean;
    render?: (item: T) => React.ReactNode;
};

export interface Pagination {
    current_page: number;
    last_page: number;
    per_page: number;
    from?: number;
    to?: number;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface DataTableProps<T> {
    columns: Column<T>[];
    data: T[];
    pagination: Pagination;
    filters: {
        search?: string;
        per_page?: number;
        order_by?: string;
        order_dir?: 'asc' | 'desc';
    };
    searchPlaceholder?: string;
    perPageOptions?: number[];
    defaultPerPage?: number;
    addNewButton?: {
        href: string;
        label: string;
    };
    onSearch: (search: string) => void;
    onPerPageChange: (perPage: number) => void;
    onPageChange: (url: string) => void;
    onSort: (columnKey: string) => void;
}

export default function DataTable<T extends Record<string, any>>(props: DataTableProps<T>) {
    const {
        columns,
        data,
        pagination,
        filters,
        searchPlaceholder,
        perPageOptions = [10, 25, 50, 100],
        defaultPerPage = 10,
        addNewButton,
        onSearch,
        onPerPageChange,
        onPageChange,
        onSort,
    } = props;

    const [searchTerm, setSearchTerm] = useState(filters.search || '');

    const handleSearchSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onSearch(searchTerm);
    };

    const handlePerPageSelect = (e: React.ChangeEvent<HTMLSelectElement>) => {
        const perPage = Math.min(parseInt(e.target.value, 10), 100);
        onPerPageChange(perPage);
    };

    return (
        <div>
            <div className={`mb-4 flex items-center ${addNewButton ? 'justify-between' : 'justify-end'}`}>
                {addNewButton && (
                    <Link href={addNewButton.href} className="rounded bg-green-500 px-4 py-2 text-white hover:bg-green-600">
                        {addNewButton.label}
                    </Link>
                )}
                <div className="flex items-center gap-4">
                    <select
                        value={filters.per_page || defaultPerPage}
                        onChange={handlePerPageSelect}
                        className="rounded border px-2 py-1"
                    >
                        {perPageOptions.map((opt) => (
                            <option key={opt} value={opt}>
                                {opt} per halaman
                            </option>
                        ))}
                    </select>
                    <form onSubmit={handleSearchSubmit} className="flex items-center space-x-2">
                        <input
                            type="text"
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="w-64 rounded border px-3 py-2"
                            placeholder={searchPlaceholder || 'Cari...'}
                        />
                        <button type="submit" className="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                            Cari
                        </button>
                    </form>
                </div>
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        {columns.map((col) => (
                            <TableHead
                                key={col.key}
                                onClick={col.sortable ? () => onSort(col.key) : undefined}
                                className={col.sortable ? 'cursor-pointer select-none' : undefined}
                            >
                                {col.label}{' '}
                                {filters.order_by === col.key
                                    ? filters.order_dir === 'asc'
                                        ? '↑'
                                        : '↓'
                                    : ''}
                            </TableHead>
                        ))}
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {data.map((item, idx) => (
                        <TableRow key={idx}>
                            {columns.map((col) => (
                                <TableCell key={col.key}>
                                    {col.render ? col.render(item) : (item as any)[col.key]}
                                </TableCell>
                            ))}
                        </TableRow>
                    ))}
                </TableBody>
            </Table>

            <div className="mt-4 flex flex-wrap items-center justify-between gap-4">
                <div className="flex gap-2">
                    {pagination.prev_page_url && (
                        <button
                            onClick={() => onPageChange(pagination.prev_page_url!)}
                            className="rounded bg-gray-200 px-3 py-1"
                        >
                            Prev
                        </button>
                    )}
                    {pagination.next_page_url && (
                        <button
                            onClick={() => onPageChange(pagination.next_page_url!)}
                            className="rounded bg-gray-200 px-3 py-1"
                        >
                            Next
                        </button>
                    )}
                </div>
                <div className="text-sm text-gray-600">
                    Menampilkan data ke {pagination.from} sampai {pagination.to} dari total {pagination.total} data. Halaman {pagination.current_page} dari {pagination.last_page}.
                </div>
            </div>
        </div>
    );
}
