import { Fragment } from 'react';
import Text from './text';
import Input from './input';
import Textarea from './textarea';
import Form from './form';
import Table from './table';
import Dialog from './dialog';

export default function renderComponent(component: any, index: number) {
    if (component.visible === false) {
        return <Fragment key={index} />;
    }

    switch (component.type) {
        case 'Text':
            return <Text key={index} field={component} />;
        case 'Input':
            return <Input key={index} field={component} />;
        case 'Textarea':
            return <Textarea key={index} field={component} />;
        case 'Form':
            return <Form key={index} component={component} />;
        case 'Table':
            return <Table key={index} field={component} />;
        case 'Dialog':
            return <Dialog key={index} field={component} />;
        default:
            return <Fragment key={index} />;
    }
}
