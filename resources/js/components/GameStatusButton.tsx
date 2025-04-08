import React, { useState } from 'react';
import axios from 'axios';

// Set up axios to include CSRF token
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
import { Button } from '@/components/ui/button';
import { ClipboardList, Check, X } from 'lucide-react';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import { Label } from '@/components/ui/label';

// These should match the PHP enum values
const GAME_STATUSES = {
  playing: 'Playing',
  beaten: 'Beaten',
  completed: 'Completed',
  on_hold: 'On Hold',
  abandoned: 'Abandoned'
};

interface GameStatusButtonProps {
  gameId: number;
  currentStatus?: string | null;
  variant?: 'default' | 'outline' | 'secondary' | 'ghost' | 'link' | 'destructive';
  size?: 'default' | 'sm' | 'lg' | 'icon';
  className?: string;
}

export function GameStatusButton({
  gameId,
  currentStatus,
  variant = 'default',
  size = 'default',
  className = ''
}: GameStatusButtonProps) {
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [selectedStatus, setSelectedStatus] = useState<string | null>(currentStatus || null);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleStatusChange = async () => {
    setIsSubmitting(true);
    
    try {
      if (selectedStatus) {
        // Update or create status
        await axios.post(`/games/${gameId}/status`, {
          status: selectedStatus,
        });
        
        // Reload the page to reflect the changes
        window.location.reload();
      } else {
        // Clear status - using POST with _method=DELETE for better compatibility
        await axios.post(`/games/${gameId}/status`, {
          _method: 'DELETE'
        });
        
        // Reload the page to reflect the changes
        window.location.reload();
      }
    } catch (error) {
      console.error('Error updating game status:', error);
      alert('An error occurred while updating the game status. Please try again.');
      setIsSubmitting(false);
    }
  };

  const getButtonText = () => {
    if (currentStatus) {
      return GAME_STATUSES[currentStatus as keyof typeof GAME_STATUSES] || 'Set Status';
    }
    return 'Set Status';
  };

  return (
    <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
      <DialogTrigger asChild>
        <Button
          variant={currentStatus ? 'default' : variant}
          size={size}
          className={className}
        >
          <ClipboardList className="h-4 w-4 mr-2" />
          {getButtonText()}
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>Game Status</DialogTitle>
          <DialogDescription>
            Track your progress with this game by setting its status.
          </DialogDescription>
        </DialogHeader>
        
        <div className="py-4">
          <ToggleGroup type="single" value={selectedStatus || ''} onValueChange={setSelectedStatus} className="flex flex-col space-y-2">
            {Object.entries(GAME_STATUSES).map(([value, label]) => (
              <div key={value} className="flex items-center">
                <ToggleGroupItem value={value} id={`status-${value}`} className="w-full justify-start px-3 py-2">
                  {label}
                </ToggleGroupItem>
              </div>
            ))}
          </ToggleGroup>
        </div>
        
        <DialogFooter className="flex justify-between">
          <Button
            variant="outline"
            onClick={() => setSelectedStatus(null)}
            disabled={isSubmitting}
          >
            <X className="h-4 w-4 mr-2" />
            Clear Status
          </Button>
          
          <Button
            onClick={handleStatusChange}
            disabled={isSubmitting}
          >
            {isSubmitting ? (
              <>
                <svg className="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                  <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Saving...
              </>
            ) : (
              <>
                <Check className="h-4 w-4 mr-2" />
                Save Status
              </>
            )}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
