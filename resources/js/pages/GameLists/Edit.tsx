import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { AppHeader } from '@/components/app-header';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { ArrowLeft, Edit as EditIcon } from 'lucide-react';

interface GameList {
  id: number;
  name: string;
  description: string | null;
  is_public: boolean;
}

interface EditProps {
  gameList: GameList;
}

export default function Edit({ gameList }: EditProps) {
  const { data, setData, put, processing, errors } = useForm({
    name: gameList.name,
    description: gameList.description || '',
    is_public: gameList.is_public,
  });

  const breadcrumbs = [
    { title: 'Home', href: '/' },
    { title: 'Game Lists', href: '/game-lists' },
    { title: gameList.name, href: `/game-lists/${gameList.id}` },
    { title: 'Edit', href: `/game-lists/${gameList.id}/edit` },
  ];

  const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    put(`/game-lists/${gameList.id}`);
  };

  return (
    <AppShell>
      <Head title={`Edit ${gameList.name}`} />
      <AppHeader breadcrumbs={breadcrumbs} />
      
      <div className="container mx-auto px-4 py-8">
        <div className="mb-6">
          <Link href={`/game-lists/${gameList.id}`}>
            <Button variant="ghost" className="pl-0 flex items-center text-blue-600 hover:text-blue-800">
              <ArrowLeft className="h-4 w-4 mr-2" />
              Back to List
            </Button>
          </Link>
        </div>
        
        <div className="flex items-center mb-8">
          <EditIcon className="h-6 w-6 mr-2 text-blue-600" />
          <Heading>Edit {gameList.name}</Heading>
        </div>
        
        <div className="max-w-2xl mx-auto">
          <Card>
            <form onSubmit={handleSubmit}>
              <CardHeader>
                <p className="text-gray-500">
                  Update your game list details.
                </p>
              </CardHeader>
              
              <CardContent className="space-y-6">
                <div className="space-y-2">
                  <Label htmlFor="name">List Name</Label>
                  <Input
                    id="name"
                    value={data.name}
                    onChange={e => setData('name', e.target.value)}
                    required
                  />
                  {errors.name && (
                    <p className="text-sm text-red-500">{errors.name}</p>
                  )}
                </div>
                
                <div className="space-y-2">
                  <Label htmlFor="description">Description (Optional)</Label>
                  <Textarea
                    id="description"
                    value={data.description}
                    onChange={e => setData('description', e.target.value)}
                    rows={4}
                  />
                  {errors.description && (
                    <p className="text-sm text-red-500">{errors.description}</p>
                  )}
                </div>
                
                <div className="flex items-center space-x-2">
                  <Checkbox
                    id="is_public"
                    checked={data.is_public}
                    onCheckedChange={(checked) => setData('is_public', !!checked)}
                  />
                  <Label htmlFor="is_public" className="cursor-pointer">
                    Make this list public
                  </Label>
                </div>
              </CardContent>
              
              <CardFooter className="flex justify-between">
                <Link href={`/game-lists/${gameList.id}`}>
                  <Button variant="outline" type="button">Cancel</Button>
                </Link>
                <Button type="submit" disabled={processing}>
                  Save Changes
                </Button>
              </CardFooter>
            </form>
          </Card>
        </div>
      </div>
    </AppShell>
  );
}
