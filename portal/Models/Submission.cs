using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace WebHackingPortal.Models
{
    public class Submission
    {
        [Key]
        public int Id { get; set; }

        [Required]
        [ForeignKey("User")]
        public int UserId { get; set; }
        public int LabId { get; set; }
        public int ChallengeId { get; set; }

        public string SubmittedFlag { get; set; } = string.Empty;
        public bool IsCorrect { get; set; }
        public DateTime Timestamp { get; set; } = DateTime.UtcNow;

        // Navigation properties (optional)
        [InverseProperty("Submissions")]
        public AppUser? User { get; set; }
    }
}