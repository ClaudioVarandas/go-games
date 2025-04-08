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
import { ArrowLeft, ListPlus } from 'lucide-react';

export default function Create() {
  const { data, setData, post, processing, errors } = useForm({
    name: '',
    description: '',
    is_public: true,
  });

  const breadcrumbs = [
    { title: 'Home', href: '/' },
    { title: 'Game Lists', href: '/game-lists' },
    { title: 'Create New List', href: '/game-lists/create' },
  ];

  const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    post('/game-lists');
  };

  return (
    <AppShell>
      <Head title="Create Game List" />
      <AppHeader breadcrumbs={breadcrumbs} />
      
      <div className="container mx-auto px-4 py-8">
        <div className="mb-6">
          <Link href="/game-lists">
            <Button variant="ghost" className="pl-0 flex items-center text-blue-600 hover:text-blue-800">
              <ArrowLeft className="h-4 w-4 mr-2" />
              Back to Game Lists
            </Button>
          </Link>
        </div>
        
        <div className="flex items-center mb-8">
          <ListPlus className="h-6 w-6 mr-2 text-blue-600" />
          <Heading>Create New Game List</Heading>
        </div>
        
        <div className="max-w-2xl mx-auto">
          <Card>
            <form onSubmit={handleSubmit}>
              <CardHeader>
                <p className="text-gray-500">
                  Create a new list to organize your favorite games. You can add games to your list from the game details page.
                </p>
              </CardHeader>
              
              <CardContent className="space-y-6">
                <div className="space-y-2">
                  <Label htmlFor="name">List Name</Label>
                  <Input
                    id="name"
                    value={data.name}
                    onChange={e => setData('name', e.target.value)}
                    placeholder="My Favorite RPGs"
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
                    placeholder="A collection of my favorite role-playing games..."
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
                <Link href="/game-lists">
                  <Button variant="outline" type="button">Cancel</Button>
                </Link>
                <Button type="submit" disabled={processing}>
                  Create List
                </Button>
              </CardFooter>
            </form>
          </Card>
        </div>
      </div>
    </AppShell>
  );
}
