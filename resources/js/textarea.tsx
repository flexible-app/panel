import { useFormContext } from './form';

export default function Textarea({ field }: { field: any }) {
    const { form, update } = useFormContext();

    return (
        <div className="mb-4">
            {field.label && (
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {field.label}
                </label>
            )}
            <textarea
                name={field.name}
                rows={field.rows || 4}
                placeholder={field.placeholder || ''}
                value={form.data[field.name] || ''}
                onChange={(e) => update(field.name, e.target.value)}
                className="w-full rounded border px-3 py-2 text-sm bg-white dark:bg-gray-900 dark:text-white dark:border-gray-700"
            />
            {form.errors[field.name] && (
                <div className="text-sm text-red-500 mt-1">{form.errors[field.name]}</div>
            )}
        </div>
    );
}
