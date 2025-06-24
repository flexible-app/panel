import React, { createContext, useContext, useEffect, useState } from 'react';
import { useForm, usePage, router } from '@inertiajs/react';
import renderComponent from './index';

const FormContext = createContext<any>(null);
export const useFormContext = () => useContext(FormContext);

export default function Form({ component }: { component: any }) {
    const { panel, page } = usePage<SharedData>().props;
    const form = useForm({});
    const update = (name: string, value: any) => form.setData(name, value);

    const [schema, setSchema] = useState(component.schema); // ✅ useState


    useEffect(() => {
        const initialData: Record<string, any> = {};

        for (const field of component.schema) {
            if (field.name) {
                initialData[field.name] = field.value ?? field.default ?? '';
            }
        }

        form.setData(initialData);
    }, []);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        const data = Object.fromEntries(
            schema
                .filter((field: any) => field.name)
                .map((field: any) => [field.name, field.value ?? field.default ?? ''])
        );

        const path = `/${panel.path}/${page.slug}/forms/${component.action}`;

        form.post(path, { preserveScroll: true });
    };

    const refreshSchema = (changedName: string, changedValue: any) => {
        const updatedData = {
            ...form.data,
            [changedName]: changedValue,
        };

        const path = `/${panel.path}/${page.slug}/forms/${component.action}/schema`;

        router.post(path, updatedData, {
            preserveScroll: true,
            onSuccess: (page) => {
                const newSchema = page.props.flash.schema;
                if (newSchema) {
                    setSchema(newSchema); // ✅ trigger re-render
                    console.log('Schema refreshed', newSchema);
                }
            },
        });
    };

    return (
        <FormContext.Provider value={{ form, update, refreshSchema }}>
            <form onSubmit={handleSubmit} className="space-y-4">
                {schema.map((child: any, idx: number) =>
                    renderComponent(child, idx)
                )}
                <button
                    type="submit"
                    className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
                    disabled={form.processing}
                >
                    Submit
                </button>
            </form>
        </FormContext.Provider>
    );
}
