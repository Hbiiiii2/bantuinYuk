import { useState } from 'react'
import { User, Star, CheckCircle, Edit2, Save, X } from 'lucide-react'
import { useHelperProfile, useRatingSummary, useUpdateProfile } from '../hooks'
import { VerificationBadge } from '../components/VerificationBadge'
import { PageHeader } from '@/components/layout/PageHeader'
import { Card, CardContent, CardHeader } from '@/components/ui/Card'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { Textarea } from '@/components/ui/Textarea'
import { SkeletonCard } from '@/components/shared/SkeletonCard'
import { ErrorState } from '@/components/shared/ErrorState'
import { getInitials, formatDateTime } from '@/lib/utils'

export function HelperProfile() {
  const { data: profile, isLoading, error, refetch } = useHelperProfile()
  const { data: ratingSummary } = useRatingSummary()
  const updateProfile = useUpdateProfile()
  
  const [isEditing, setIsEditing] = useState(false)
  const [editForm, setEditForm] = useState({
    bio: '',
    skills: ''
  })
  
  const handleEdit = () => {
    if (profile) {
      setEditForm({
        bio: profile.bio || '',
        skills: profile.skills || ''
      })
      setIsEditing(true)
    }
  }
  
  const handleSave = async () => {
    try {
      await updateProfile.mutateAsync(editForm)
      setIsEditing(false)
      refetch()
    } catch {
      // Error handled by mutation
    }
  }
  
  const handleCancel = () => {
    setIsEditing(false)
  }
  
  if (isLoading) {
    return (
      <div>
        <PageHeader title="Profile" />
        <SkeletonCard />
      </div>
    )
  }
  
  if (error) {
    return <ErrorState message="Failed to load profile" onRetry={refetch} />
  }
  
  if (!profile) {
    return <ErrorState message="Profile not found" />
  }
  
  return (
    <div>
      <PageHeader 
        title="Profile" 
        actions={
          !isEditing ? (
            <Button variant="secondary" size="sm" onClick={handleEdit}>
              <Edit2 size={14} className="mr-1" />
              Edit
            </Button>
          ) : (
            <>
              <Button variant="ghost" size="sm" onClick={handleCancel}>
                <X size={14} />
              </Button>
              <Button size="sm" onClick={handleSave} loading={updateProfile.isPending}>
                <Save size={14} className="mr-1" />
                Save
              </Button>
            </>
          )
        }
      />
      
      <div className="space-y-4">
        {/* Profile Info */}
        <Card>
          <CardContent>
            <div className="flex items-center gap-4">
              <div className="w-16 h-16 rounded-full bg-primary flex items-center justify-center text-white text-xl font-bold">
                {profile.user?.photo ? (
                  <img 
                    src={profile.user.photo} 
                    alt={profile.user.name}
                    className="w-full h-full rounded-full object-cover"
                  />
                ) : (
                  getInitials(profile.user?.name || 'H')
                )}
              </div>
              <div className="flex-1">
                <h2 className="text-lg font-semibold text-gray-900">{profile.user?.name}</h2>
                <p className="text-sm text-gray-500">{profile.user?.email}</p>
                <VerificationBadge status={profile.verification_status} className="mt-1" />
              </div>
            </div>
          </CardContent>
        </Card>
        
        {/* Rating Summary */}
        <Card>
          <CardHeader>
            <h3 className="font-medium text-gray-900 flex items-center gap-2">
              <Star size={16} />
              Rating Summary
            </h3>
          </CardHeader>
          <CardContent>
            <div className="flex items-center gap-4">
              <div className="text-center">
                <p className="text-3xl font-bold text-warning">
                  {ratingSummary?.average_rating?.toFixed(1) || '0.0'}
                </p>
                <p className="text-xs text-gray-500">Average</p>
              </div>
              <div className="flex-1 space-y-1">
                {[5, 4, 3, 2, 1].map((star) => {
                  const count = ratingSummary?.distribution?.[star as keyof typeof ratingSummary.distribution] || 0
                  const total = ratingSummary?.total_reviews || 1
                  const percentage = (count / total) * 100
                  
                  return (
                    <div key={star} className="flex items-center gap-2">
                      <span className="text-xs text-gray-500 w-3">{star}</span>
                      <Star size={12} className="text-warning fill-warning" />
                      <div className="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div 
                          className="h-full bg-warning rounded-full"
                          style={{ width: `${percentage}%` }}
                        />
                      </div>
                      <span className="text-xs text-gray-500 w-6">{count}</span>
                    </div>
                  )
                })}
              </div>
            </div>
            <div className="mt-3 pt-3 border-t border-gray-100 flex justify-between text-sm">
              <span className="text-gray-500">Total Reviews</span>
              <span className="font-medium">{ratingSummary?.total_reviews || 0}</span>
            </div>
          </CardContent>
        </Card>
        
        {/* Bio */}
        <Card>
          <CardHeader>
            <h3 className="font-medium text-gray-900 flex items-center gap-2">
              <User size={16} />
              Bio
            </h3>
          </CardHeader>
          <CardContent>
            {isEditing ? (
              <Textarea
                value={editForm.bio}
                onChange={(e) => setEditForm({ ...editForm, bio: e.target.value })}
                placeholder="Tell us about yourself..."
              />
            ) : (
              <p className="text-gray-600">
                {profile.bio || 'No bio added yet'}
              </p>
            )}
          </CardContent>
        </Card>
        
        {/* Skills */}
        <Card>
          <CardHeader>
            <h3 className="font-medium text-gray-900">Skills</h3>
          </CardHeader>
          <CardContent>
            {isEditing ? (
              <Input
                value={editForm.skills}
                onChange={(e) => setEditForm({ ...editForm, skills: e.target.value })}
                placeholder="e.g., Plumbing, Electrical, Cleaning"
              />
            ) : (
              <p className="text-gray-600">
                {profile.skills || 'No skills added yet'}
              </p>
            )}
          </CardContent>
        </Card>
        
        {/* Stats */}
        <Card>
          <CardContent>
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <CheckCircle size={16} className="text-success" />
                <span className="text-gray-600">Completed Tasks</span>
              </div>
              <span className="font-semibold">{profile.completed_tasks}</span>
            </div>
          </CardContent>
        </Card>
        
        {/* Member Since */}
        <Card>
          <CardContent>
            <p className="text-sm text-gray-500">
              Member since {formatDateTime(profile.created_at)}
            </p>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
