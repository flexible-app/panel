import React from 'react';
import renderComponent from './index';

import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog"

export default function DialogUi({ field }: { field: any }) {
    // const { open, name, schema } = field;
    return (
        <Dialog>
          <DialogTrigger>Open</DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Are you absolutely sure?</DialogTitle>
              <DialogDescription>
                This action cannot be undone. This will permanently delete your account
                and remove your data from our servers.
              </DialogDescription>
            <div className="space-y-4">
                { field.schema.map((child: any, idx: number) => renderComponent(child, idx)) }
            </div>
            </DialogHeader>
          </DialogContent>
        </Dialog>
    );
}