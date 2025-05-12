using System;
using System.ComponentModel.DataAnnotations;

namespace WebHackingPortal.Models
{
    public class Submission
    {
        [Key]
        public int Id { get; set; }

        public int UserId { get; set; }
        public AppUser? User { get; set; }

        public int ChallengeId { get; set; }
        public Challenge? Challenge { get; set; }

        [Required]
        public string SubmittedFlag { get; set; } = string.Empty;

        public bool IsCorrect { get; set; }

        public DateTime Timestamp { get; set; } = DateTime.UtcNow;
    }
}