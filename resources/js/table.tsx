import React from 'react'
import { router, usePage } from '@inertiajs/react'
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table"
import type { SharedData } from '@/types'

export default function CustomTable({ field }: { field: any }) {
    const { columns, rows, pagination, actions } = field

    if (!columns?.length) return null

    console.log('columns', columns);
    console.log('rows', rows);
    console.log('actions', actions);
    
    const reloadPage = (page: number) => {
        const { panel, page: currentPage } = usePage<SharedData>().props

        router.visit(`/${panel.path}/${currentPage.slug}`, {
            data: { page },
            preserveScroll: true,
            preserveState: true,
            only: ['page'],
        })
    }

    return (
        <div className="my-5 overflow-x-auto rounded border border-border rounded-lg">
            <Table>
                <TableCaption>Table data preview</TableCaption>
                <TableHeader>
                    <TableRow>
                        {columns.map((col: any, i: number) => (
                            <TableHead key={i} className="ps-4">
                                {typeof col === 'string' ? col : col.label}
                            </TableHead>
                        ))}
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {rows.map((row: any, rowIndex: number) => (
                        <TableRow key={rowIndex}>
                            {columns.map((col: any, colIndex: number) => {
                                const key = typeof col === 'string' ? col : col.key
                                return (
                                    <TableCell key={colIndex} className="ps-4">
                                        {row[key] ?? ''}
                                    </TableCell>
                                )
                            })}
                        </TableRow>
                    ))}
                </TableBody>

                {pagination && (
                    <TableFooter>
                        <TableRow>
                            <TableCell colSpan={columns.length}>
                                <div className="flex items-center justify-between text-sm">
                                    <span>
                                        Page {pagination.current_page} of {pagination.last_page}
                                    </span>
                                    <div className="space-x-2">
                                        {pagination.current_page > 1 && (
                                            <button
                                                onClick={() => reloadPage(pagination.current_page - 1)}
                                                className="px-3 py-1 rounded border"
                                            >
                                                Prev
                                            </button>
                                        )}
                                        {pagination.current_page < pagination.last_page && (
                                            <button
                                                onClick={() => reloadPage(pagination.current_page + 1)}
                                                className="px-3 py-1 rounded border"
                                            >
                                                Next
                                            </button>
                                        )}
                                    </div>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableFooter>
                )}
            </Table>
        </div>
    )
}
