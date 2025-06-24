export default function Text({ field }: { field: any }) {
    return (
        <p className="text-base text-gray-800 dark:text-gray-200">
            {field.label && <strong>{field.label}: </strong>}
            {field.value}
        </p>
    );
}
