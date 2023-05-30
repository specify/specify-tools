/** Misc utility functions used by other scripts */
import notifier from 'node-notifier';
import { spawn } from 'child_process';

/** Send a notification using the node-notifier module */
export function send_notification(
  title: string,
  message: string,
  // Sounds are coming from /System/Library/Sounds/
  sound = 'Pop'
): void {
  notifier.notify({
    title,
    message,
    timeout: 2,
  });
  spawn('afplay', [`/System/Library/Sounds/${sound}.aiff`,'--volume','0.2'], {
    detached: true,
  });
}
