import { useFormContext } from './form';
import { useState } from 'react';

export default function Input({ field }: { field: any }) {
    let context;
    try {
        context = useFormContext();
    } catch {
        context = null;
    }

    const [localValue, setLocalValue] = useState(field.default ?? '');

    const form = context?.form;
    const update = context?.update ?? ((_: string, value: any) => setLocalValue(value));
    const refreshSchema = context?.refreshSchema ?? (() => {});

    const value = form ? form.data[field.name] ?? '' : localValue;

    return (
        <div className="mb-4">
            {field.label && (
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {field.label}
                </label>
            )}
            <input
                type="text"
                name={field.name}
                placeholder={field.placeholder || ''}
                value={value}
                onChange={(e) => {
                    const val = e.target.value;
                    update(field.name, val);
                    if (field.reactive) refreshSchema(field.name, val);
                }}
                className="w-full rounded border px-3 py-2 text-sm bg-white dark:bg-gray-900 dark:text-white dark:border-gray-700"
            />
            {form?.errors[field.name] && (
                <div className="text-sm text-red-500 mt-1">{form.errors[field.name]}</div>
            )}
        </div>
    );
}
