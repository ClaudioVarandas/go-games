import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ListPlus, Plus, ArrowLeft } from 'lucide-react';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Card, CardContent } from '@/components/ui/card';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';

interface GameList {
  id: number;
  name: string;
  description?: string;
}

interface AddToListButtonProps {
  gameId: number;
  gameLists: GameList[];
  variant?: 'default' | 'outline' | 'secondary' | 'ghost' | 'link' | 'destructive';
  size?: 'default' | 'sm' | 'lg' | 'icon';
  className?: string;
}

export function AddToListButton({
  gameId,
  gameLists,
  variant = 'default',
  size = 'default',
  className = ''
}: AddToListButtonProps) {
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [viewMode, setViewMode] = useState<'select' | 'create'>('select');
  const [newListName, setNewListName] = useState('');
  const [newListDescription, setNewListDescription] = useState('');
  const [newListType, setNewListType] = useState<'regular' | 'backlog' | 'wishlist'>('regular');
  const [isPublic, setIsPublic] = useState(true);
  const [notes, setNotes] = useState('');
  const [selectedListId, setSelectedListId] = useState<number | null>(null);
  const [isAddingToList, setIsAddingToList] = useState(false);
  const [isCreatingList, setIsCreatingList] = useState(false);

  const handleCreateList = () => {
    setIsCreatingList(true);

    router.post('/game-lists', {
      name: newListName,
      description: newListDescription,
      type: newListType,
      is_public: isPublic,
    }, {
      onSuccess: (response: any) => {
        // Get the new list ID from the response
        const newListId = response?.props?.gameList?.id;

        if (newListId) {
          // Add the game to the newly created list
          addGameToList(newListId);

          // Close the dialog and reset form
          setIsDialogOpen(false);
          setNewListName('');
          setNewListDescription('');
          setNotes('');
          setViewMode('select');

          // Show a success message (you could use a toast notification library here)
          console.log('Game added to new list successfully');
        } else {
          console.error('Failed to get new list ID from response', response);
          alert('Failed to get new list ID from response');
        }

        setIsCreatingList(false);
      },
      onError: (errors: any) => {
        console.error('Error creating list:', errors);
        setIsCreatingList(false);
      }
    });
  };

  const addGameToList = (listId: number) => {
    setIsAddingToList(true);

    fetch(`/game-lists/${listId}/games`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
      },
      body: JSON.stringify({
        game_id: gameId,
        notes: notes,
      }),
    })
      .then(async (response) => {
        if (!response.ok) {
          const errorData = await response.json();
          throw errorData;
        }
        return response.json();
      })
      .then(() => {
        setIsAddingToList(false);
        setNotes('');
        setSelectedListId(null);
        setIsDialogOpen(false);
      })
      .catch((error) => {
        console.error('Error adding game to list:', error);
        alert(`Error: ${Object.values(error).flat().join(', ')}`);
        setIsAddingToList(false);
      });
  };

  const handleAddToSelectedList = () => {
    if (selectedListId) {
      addGameToList(selectedListId);
    }
  };

  const resetForm = () => {
    setNewListName('');
    setNewListDescription('');
    setIsPublic(true);
    setNotes('');
    setSelectedListId(null);
    setViewMode('select');
  };

  const handleDialogOpenChange = (open: boolean) => {
    setIsDialogOpen(open);
    if (!open) {
      resetForm();
    }
  };

  // Render the dialog content based on the view mode
  const renderDialogContent = () => {
    if (viewMode === 'select' && gameLists.length > 0) {
      return (
        <>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[300px] overflow-y-auto py-2">
            {gameLists.map((list) => (
              <Card
                key={list.id}
                className={`cursor-pointer transition-all ${selectedListId === list.id ? 'border-blue-500 ring-2 ring-blue-500' : 'hover:border-gray-400'}`}
                onClick={() => setSelectedListId(list.id)}
              >
                <CardContent className="p-4">
                  <h3 className="font-medium">{list.name}</h3>
                  {list.description && (
                    <p className="text-sm text-gray-500 mt-1 line-clamp-2">{list.description}</p>
                  )}
                </CardContent>
              </Card>
            ))}
          </div>

          <div className="flex justify-between items-center mt-4">
            <Button
              variant="outline"
              onClick={() => setViewMode('create')}
            >
              <Plus className="h-4 w-4 mr-2" />
              Create New List
            </Button>

            <Button
              onClick={handleAddToSelectedList}
              disabled={!selectedListId || isAddingToList}
            >
              {isAddingToList ? (
                <>
                  <svg className="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Adding...
                </>
              ) : (
                'Add to Selected List'
              )}
            </Button>
          </div>
        </>
      );
    } else {
      // Create new list form or no lists exist
      return (
        <>
          {gameLists.length > 0 && (
            <Button
              variant="ghost"
              className="mb-4"
              onClick={() => setViewMode('select')}
            >
              <ArrowLeft className="h-4 w-4 mr-2" />
              Back to Lists
            </Button>
          )}

          <div className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="name">List Name</Label>
              <Input
                id="name"
                value={newListName}
                onChange={(e) => setNewListName(e.target.value)}
                placeholder="My Favorite Games"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="description">Description (Optional)</Label>
              <Textarea
                id="description"
                value={newListDescription}
                onChange={(e) => setNewListDescription(e.target.value)}
                placeholder="A collection of my favorite games..."
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="notes">Notes about this game (Optional)</Label>
              <Textarea
                id="notes"
                value={notes}
                onChange={(e) => setNotes(e.target.value)}
                placeholder="Why you're adding this game to the list..."
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="list_type">List Type</Label>
              <select
                id="list_type"
                value={newListType}
                onChange={(e) => setNewListType(e.target.value as 'regular' | 'backlog' | 'wishlist')}
                className="w-full border border-gray-300 rounded px-3 py-2"
              >
                <option value="regular">Regular</option>
                <option value="backlog">Backlog</option>
                <option value="wishlist">Wishlist</option>
              </select>
            </div>

            <div className="flex items-center space-x-2">
              <input
                type="checkbox"
                id="is_public"
                checked={isPublic}
                onChange={(e) => setIsPublic(e.target.checked)}
                className="rounded border-gray-300"
              />
              <Label htmlFor="is_public">Make this list public</Label>
            </div>
          </div>

          <div className="flex justify-end mt-4">
            <Button
              onClick={handleCreateList}
              disabled={!newListName || isCreatingList}
            >
              {isCreatingList ? (
                <>
                  <svg className="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Creating...
                </>
              ) : 'Create & Add Game'}
            </Button>
          </div>
        </>
      );
    }
  };

  // Determine the dialog title and description based on the view mode
  const getDialogHeader = () => {
    if (viewMode === 'select' && gameLists.length > 0) {
      return {
        title: 'Add Game to List',
        description: 'Select a list to add this game to, or create a new list.'
      };
    } else {
      return {
        title: 'Create New List',
        description: 'Create a new list and add this game to it.'
      };
    }
  };

  const dialogHeader = getDialogHeader();

  return (
    <Dialog open={isDialogOpen} onOpenChange={handleDialogOpenChange}>
      <DialogTrigger asChild>
        <Button
          variant={variant}
          size={size}
          className={className}
        >
          <ListPlus className="h-4 w-4 mr-2" />
          Add to List
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>{dialogHeader.title}</DialogTitle>
          <DialogDescription>
            {dialogHeader.description}
          </DialogDescription>
        </DialogHeader>

        {gameLists.length > 0 && (
          <div className="mb-4">
            <ToggleGroup type="single" value={viewMode} onValueChange={(value) => value && setViewMode(value as 'select' | 'create')}>
              <ToggleGroupItem value="select" aria-label="Select List">
                Select List
              </ToggleGroupItem>
              <ToggleGroupItem value="create" aria-label="Create New List">
                Create New List
              </ToggleGroupItem>
            </ToggleGroup>
          </div>
        )}

        {renderDialogContent()}
      </DialogContent>
    </Dialog>
  );
}
